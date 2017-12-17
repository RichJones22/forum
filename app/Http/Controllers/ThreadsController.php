<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Channel;
use App\Filters\ThreadFilters;
use App\Thread;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class ThreadsController.
 */
class ThreadsController extends Controller
{
    const paginationCount = 5;

    /**
     * @var Thread
     */
    private $thread;
    /**
     * @var User
     */
    private $user;
    /**
     * @var Request
     */
    private $request;

    /**
     * ThreadsController constructor.
     *
     * @param Thread  $thread
     * @param User    $user
     * @param Request $request
     *
     * @internal param Channel $channel
     */
    public function __construct(Thread $thread, User $user, Request $request)
    {
        $this->setThread($thread);
        $this->setUser($user);

        $this->middleware('auth')->except(['index', 'show']);
        $this->setRequest($request);
    }

    /**
     * @param Channel       $channel
     * @param ThreadFilters $threadFilters
     *
     * @return \Illuminate\Contracts\View\Factory|Collection|\Illuminate\View\View
     */
    public function index(Channel $channel, ThreadFilters $threadFilters)
    {
        $threads = $this->getThreads($channel, $threadFilters);

        if ($this->getRequest()->wantsJson()) {
            return $threads;
        }

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
     * @param $channel
     * @param Thread $thread
     *
     * @return Thread
     */
    public function show($channel, Thread $thread)
    {
        return view('threads.show', [
            'thread' => $thread,
            'replies' => $thread->replies()->paginate($this::paginationCount),
        ]);
    }

    /**
     * @param Thread $thread
     */
    public function edit(Thread $thread)
    {
    }

    /**
     * @param Request $request
     * @param Thread  $thread
     */
    public function update(Request $request, Thread $thread)
    {
    }

    /**
     * @param $channel
     * @param Thread $thread
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function destroy($channel, Thread $thread)
    {
        try {
            DB::transaction(function () use ($thread) {
                $thread->delete();
            });
        } catch (Throwable $t) {
            return response('failed deleting thread...', 500);
        }

        if (request()->wantsJson()) {
            return response([], 204);
        }

        return redirect('/threads');
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

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     *
     * @return ThreadsController
     */
    public function setRequest(Request $request): ThreadsController
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @param Channel       $channel
     * @param ThreadFilters $threadFilters
     *
     * @return Collection
     */
    protected function getThreads(Channel $channel, ThreadFilters $threadFilters)
    {
        /** @var Builder $threadBuilder */
        $threadBuilder = $this
            ->getThread()
            ->newQuery()
            ->latest();

        if ($channel->exists) {
            $threadBuilder->where('channel_id', $channel->id);
        }

//        dd($threadBuilder->toSql());

        // not liking the scopes things on the Models...
        // seems too hidden to me...
//        $threads = $threadBuilder->filter($threadFilters)->get();

        // here is a more direct way of calling the filter...
        /** @var Collection $threads */
        $threads = $this->getThread()
            ->scopeFilter($threadBuilder, $threadFilters)
            ->get();

        return $threads;
    }
}
