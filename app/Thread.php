<?php

declare(strict_types=1);

namespace App;

use App\Filters\ThreadFilters;
use App\Http\Caching\PremiseCache;
use App\Notifications\ThreadWasUpdated;
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
     * @var array
     */
    protected $appends = ['isSubscribedTo'];

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
        // off of the Thread table itself.
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

                    // delete favorites
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
        /** @var Reply $reply */
        $reply = $this->replies()->create($reply);

        $this->notifySubscribers($reply);

        return $reply;
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
     *
     * @return Thread
     */
    public function subscribe(int $userId = null)
    {
        $this->subscriptions()->create([
           'user_id' => $userId ?: auth()->id(),
        ]);

        return $this;
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    /**
     * @return bool
     */
    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists();
    }

    /**
     * @param $reply
     */
    protected function notifySubscribers($reply): void
    {
        $subscriptions = $this->subscriptions()->get();

        // see episode 43 around the 16 minute mark...
        // technique 1 and 2 below yield the same results...
        // technique 1; using collections
//        $subscriptions->filter(function($sub) use ($reply){
//            return $sub->user_id != $reply->user_id;
//        })
//      -- there is also a high order technique as well
//      -- so refer to the video; episode 43 around the 16 minute mark...
//        ->each(function($sub) use ($reply) {
//            $sub->user->notify(new ThreadWasUpdated($this, $reply));
//        });

        // technique 2 -- preferred; plan old foreach loop
        // prepare notifications for all subscribers
        foreach ($subscriptions as $subscription) {
            if ($subscription->user_id !== $reply->user_id) {
                $subscription->user->notify(new ThreadWasUpdated($this, $reply));
            }
        }

        // yet another technique is the below
        // I don't like this because it uses a magic method each
//        $this->subscriptions()
//            ->where('user_id', '!=', $reply->user_id)
//            ->each
//            ->notify($reply);
    }
}
