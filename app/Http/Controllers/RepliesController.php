<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Reply;
use App\Thread;

/**
 * Class RepliesController.
 */
class RepliesController extends Controller
{
    /**
     * RepliesController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param $channelId
     * @param Thread $thread
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($channelId, Thread $thread)
    {
        $this->validate(request(), [
            'body' => 'required',
        ]);

        $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id(),
        ]);

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
