<?php

declare(strict_types=1);

namespace App;

use App\Filters\ThreadFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Thread.
 */
class Thread extends Model
{
    use RecordsActivity;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $with = ['creator', 'channel'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // weird... need the below Activity() call, otherwise the call to
        // the trait bootRecordsActivity() will not work?
        app(Activity::class);

        parent::boot();
    }

    public static function boot()
    {
        // exposes Threads->replies_count to all queries...
        static::addGlobalScope('replyCount', function (Builder $builder) {
            $builder->withCount('replies');
        });

        // when deleting a thread, delete all associated replies...
        // this is a model event handler; I'm also tempted to do it as a cascading delete
        // off of the Tread table itself.
        static::deleting(function (Thread $thread) {
            $thread->replies()->first()->delete();
        });
    }

    /**
     * @return string
     */
    public function path()
    {
        $myPath = "/threads/{$this->channel->slug}/{$this->getKey()}";

        return $myPath;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @param $reply
     */
    public function addReply($reply)
    {
        $this->replies()->create($reply);
    }

    /**
     * @param $query
     * @param ThreadFilters $filters
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, ThreadFilters $filters)
    {
        return $filters->apply($query);
    }
}
