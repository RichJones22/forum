<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Channel;
use App\Filters\ThreadFilters;
use App\Thread;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

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
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
           'title' => 'required',
           'body' => 'required',
           'channel_id' => 'required|exists:channels,id',
        ]);

        /** @var Thread $thread */
        $thread = $this->getThread()->newQuery()->create([
            'user_id' => auth()->id(),
            'channel_id' => $request->get('channel_id'),
            'title' => $request->get('title'),
            'body' => $request->get('body'),
        ]);

        return redirect($thread->path())
            ->with('flash', 'Your thread has been published!');
    }

    /**
     * @param $channel
     * @param Thread $thread
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * @throws \Exception
     */
    public function show($channel, Thread $thread)
    {
        $this->cacheThatTheThreadWasVisited($thread);

        return view('threads.show', [
            'thread' => $thread,
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
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|Redirector|Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($channel, Thread $thread)
    {
        // this calls the AuthServiceProvider Auth:before() method
        $this->authorize('update', $thread);

        try {
            \DB::transaction(function () use ($thread) {
                // Note: that the below delete() method is calling the model
                // event handler (deleting), which will delete the replies for
                // this thread.
                $thread->delete();
            });
        } catch (Throwable $t) {
            return response("failed deleting thread... File: {$t->getFile()} Line: {$t->getLine()}", 500);
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
    public function setThread(Thread $thread): self
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
    public function setUser(User $user): self
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
    public function setRequest(Request $request): self
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

    /**
     * @param Thread $thread
     *
     * @throws \Exception
     */
    protected function cacheThatTheThreadWasVisited(Thread $thread): void
    {
        /*
         * - This method stores that the user has clicked on a thread.
         * - This allows for the bold and un-bold of the threads title
         *   as a visual queue that the user has new threads.
         */

        if (null === ($user = auth()->user())) {
            $user = new User();
        }

        // if we don't have a user id, no need to cache the thread visit...
        if (null !== $user->id) {
            $key = $user->visitedThreadCacheKey($thread);
            cache()->forever($key, Carbon::now());
        }
    }
}
