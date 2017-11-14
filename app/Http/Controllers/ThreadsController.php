<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Channel;
use App\Thread;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class ThreadsController.
 */
class ThreadsController extends Controller
{
    /**
     * @var Thread
     */
    private $thread;
    /**
     * @var User
     */
    private $user;

    /**
     * ThreadsController constructor.
     *
     * @param Thread $thread
     *
     * @param User $user
     * @internal param Channel $channel
     */
    public function __construct(Thread $thread, User $user)
    {
        $this->setThread($thread);
        $this->setUser($user);

        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Channel $channel
     *
     * @return \Illuminate\Http\Response
     *
     * @internal param null $channelSlug
     */
    public function index(Channel $channel)
    {
        /* @var Collection $threads */
        if ($channel->exists) {
            $threads = $channel
                ->threads()
                ->latest();
        } else {
            $threads = $this
                ->getThread()
                ->newQuery()
                ->latest();
        }

        if ($username = request('by')) {
            $user = $this
                ->getUser()
                ->newQuery()
                ->where('name', $username)
                ->firstOrFail();

            $threads->where('user_id', $user->id);
        }

        /** @var Builder $threads */
        $threads = $threads->get();

        return view('threads.index', compact('threads'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
           'title' => 'required',
           'body' => 'required',
           'channel_id' => 'required|exists:channels,id',
        ]);

        /** @var Thread $thread */
        $thread = $this->getThread()->newInstance([
            'user_id' => auth()->id(),
            'channel_id' => $request->get('channel_id'),
            'title' => $request->get('title'),
            'body' => $request->get('body'),
        ]);

        $thread->save();

        return redirect($thread->path());
    }

    /**
     * @param $channelId
     * @param Thread $thread
     *
     * @return Thread
     */
    public function show($channelId, Thread $thread)
    {
        return view('threads.show', compact('thread'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Thread $thread
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Thread              $thread
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Thread $thread)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Thread $thread
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thread $thread)
    {
    }

    /**
     * @return Thread
     */
    public function getThread(): Thread
    {
        return $this->thread;
    }

    /**
     * @param Thread $thread
     *
     * @return ThreadsController
     */
    public function setThread(Thread $thread): ThreadsController
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return ThreadsController
     */
    public function setUser(User $user): ThreadsController
    {
        $this->user = $user;

        return $this;
    }
}
