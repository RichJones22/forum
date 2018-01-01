<?php

declare(strict_types=1);

namespace App;

use App\Http\Controllers\ThreadsController;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Reply.
 */
class Reply extends Model
{
    use Favorites, RecordsActivity;

    /**
     * @var array
     */
    protected $localAttributes;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $with = ['owner', 'favorites'];

    /**
     * Reply constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->localAttributes = ['user_id' => auth()->id()];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function path()
    {
        $page = $this->determinePageForReplyId();

        return $this->thread->path() . "?page={$page}#reply-{$this->id}";
    }

    /**
     * playing with this idea... not sure if this will help with mocking
     * a test case...
     *
     * @param Reply|null $reply
     * @return Reply
     */
    protected function getReply(?Reply $reply = null): Reply
    {
        if ($reply == null) return new Reply();

        return $reply;
    }

    /**
     * @return float|int
     */
    protected function determinePageForReplyId()
    {
        // TODO: This is not preferment; we don't want to do this all the time.
        // TODO: if we have to, we can cache this...  which is not a bad idea.
        // TODO: you will need to incorporate a cache flush schema, as the
        // TODO: reply / favorite can be deleted...
        $count = ($this->getReply())
            ->newQuery()
            ->where('thread_id', $this->thread->id)
            ->where('id', '<=', $this->id)
            ->count();

        if ($count == 0) {
            $count = 1;
        }

        $page = (int)$count / ThreadsController::paginationCount;

        if ($page == 0) {
            $page = 1;
        }
        return $page;
    }
}
