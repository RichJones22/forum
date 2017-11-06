<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Thread;
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
     * ThreadsController constructor.
     *
     * @param Thread $thread
     */
    public function __construct(Thread $thread)
    {
        $this->setThread($thread);

        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /** @var Collection $threads */
        $threads = $this
            ->getThread()
            ->newQuery()
            ->latest()
            ->get();

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
}
