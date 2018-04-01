<?php

declare(strict_types=1);

namespace App;

use App\Http\Caching\PremiseCache;
use App\Http\Controllers\RepliesController;
use App\Http\Controllers\ThreadsController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected static function boot()
    {
        parent::boot();

        static::created(function (Reply $reply) {
            $reply->thread()->increment('replies_count');
        });

        static::deleted(function (Reply $reply) {
            $reply->thread()->decrement('replies_count');
        });
    }

    /**
     * appends to the model; great for using in js on the client side...
     * - favoritesCount below uses the "get'FavoritesCount'Attribute" method
     *   on Favorites Trait.
     *
     * @var array
     */
    protected $appends = ['favoritesCount', 'isFavorited'];

    /**
     * @var PremiseCache
     */
    private $cache;

    /**
     * Reply constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->localAttributes = ['user_id' => auth()->id()];
        $this->setCache(app(PremiseCache::class));
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

    /**
     * @return mixed|string
     */
    public function path()
    {
        /** @var PremiseCache $cache */
        $cache = $this->getCache();

        // get cache key
        $key = $cache->cacheKeyForActivity(auth()->id(), $this->thread->id, $this->thread->id);

        // check if key in cache; return value if found.
        if ($value = $cache->getCache($key)) {
            return $value;
        }

        // determine page and path to route to...
        $page = $this->determinePageForReplyId();
        $path = $this->determinePath($page);

        // persist path to cache.
        $cache->persistCache($path);

        return $path;
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
        $count = $this->getReply()
            ->newQuery()
            ->where('thread_id', $this->thread->id)
            ->where('id', '<=', $this->id)
            ->count();

        if ($count == 0) {
            $count = 1;
        }

        // TODO: we should through an error if paginationCount is 0
        // TODO: where and how do we display this error?
        $page = (int)($count / RepliesController::paginationCount);

        if ($page <= 0) {
            $page = 1;
        }
        return $page;
    }

    /**
     * @return PremiseCache
     */
    public function getCache(): ?PremiseCache
    {
        return $this->cache;
    }

    /**
     * @param PremiseCache|null $cache
     * @return Reply
     */
    public function setCache(?PremiseCache $cache): Reply
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * @param $page
     * @return string
     */
    protected function determinePath($page): string
    {
        $path = $this->thread->path() . "?page={$page}#reply-{$this->id}";

        return $path;
    }

    /**
     * @return string
     */
    protected function determineCacheKey(): string
    {
        $threadCacheKey = auth()->id() . ":" . $this->thread->id . ":" . $this->id;

        return $threadCacheKey;
    }
}
