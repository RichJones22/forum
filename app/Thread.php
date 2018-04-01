<?php

declare(strict_types=1);

namespace App;

use App\Filters\ThreadFilters;
use App\Http\Caching\PremiseCache;
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

    /**
     * Thread constructor.
     *
     * @param array $attributes
     */
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
//        static::addGlobalScope('replyCount', function (Builder $builder) {
//            $builder->withCount('replies');
//        });

        // when deleting a thread, delete all associated replies...
        // this is a model event handler; I'm also tempted to do it as a cascading delete
        // off of the Tread table itself.
        // TODO:  the below seems a bit excessive; is there a better way?
        static::deleting(function (Thread $thread) {
            if ($thread->replies()->exists()) {
                $cache = app(PremiseCache::class);
                $activity = app(Activity::class);
                $favorite = app(Favorite::class);

                $thread->replies()->each(function (Reply $x) use ($cache, $activity, $favorite) {
                    // clear the cache
                    $key = $cache->cacheKeyForActivity(auth()->id(), $x->thread->id, $x->id);
                    $cache->RemovePathFromCache($key);

                    // get all activity subject_type as collection
                    $activitySubjectType = $activity->newQuery()
                        ->select('subject_type')
                        ->where('subject_id', $x->id)
                        ->get();

                    // delete activity by subject_type
                    foreach ($activitySubjectType->all() as $act) {
                        $activity->newQuery()
                            ->where('subject_type', $act->subject_type)
                            ->where('subject_id', $x->id)
                            ->delete();
                    }

                    $favorite->newQuery()
                        ->where('favorited_id', $x->id)
                        ->delete();

                    // delete the reply...
                    $x->delete();
                });
            }
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
     *
     * @return Model
     */
    public function addReply($reply)
    {
        return $this->replies()->create($reply);
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

    /**
     * @return mixed
     */
    public function getRepliesCount()
    {
        return $this->replies_count;
    }

    /**
     * @param int|null $userId
     */
    public function subscribe(int $userId = null)
    {
        $this->subscriptions()->create([
           'user_id' => $userId ?: auth()->id(),
        ]);
    }

    /**
     * @param int|null $userId
     */
    public function unsubscribe(int $userId = null)
    {
        $this
            ->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }
}
