<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Thread;
use Illuminate\Http\Request;

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
        $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id(),
        ]);

        return back();
    }
}
