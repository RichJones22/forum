<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Spam\Spam;
use App\Reply;
use App\Thread;

/**
 * Class RepliesController.
 */
class RepliesController extends Controller
{
    const paginationCount = 5;

    /**
     * RepliesController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    /**
     * @param string $channelId
     * @param Thread $thread
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(string $channelId, Thread $thread)
    {
        $page = (int) request()->query('page');

        $pageSet = $thread
            ->replies()
            ->paginate(
                self::paginationCount,
                null,
                null,
                (int) $page
            );

        return $pageSet;
    }

    /**
     * @param $channelId
     * @param Thread $thread
     * @param Spam   $spam
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Http\RedirectResponse
     *
     * @throws \Exception
     */
    public function store($channelId, Thread $thread, Spam $spam)
    {
        $this->validate(request(), [
            'body' => 'required',
        ]);

        $spam->detect(request('body'));

        $reply = $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id(),
        ]);

        if (request()->expectsJson()) {
            return $reply->load('owner');
        }

        return back()->with('flash', 'Your reply has been left.');
    }

    /**
     * @param Reply $reply
     *
     * @return $this|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function update(Reply $reply)
    {
        if ($reply->user_id !== auth()->id()) {
            return response([], 403);
        }

        $reply->update(['body' => request('body')]);

        if (request()->expectsJson()) {
            return response(['status' => 'Reply Updated']);
        }

        return $this;
    }

    /**
     * @param Reply $reply
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Exception
     */
    public function destroy(Reply $reply)
    {
        if ((int) $reply->user_id !== auth()->id()) {
            return response([], 403);
        }
        $reply->delete();
        if ($reply->isFavorited()) {
            $reply->unfavorite();
        }

        if (request()->expectsJson()) {
            return response(['status' => 'Reply deleted']);
        }

        return back();
    }
}
