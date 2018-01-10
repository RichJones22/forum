<?php

declare(strict_types=1);

namespace App\Http\Caching;

use Illuminate\Support\Facades\Cache;

/**
 * Class PremiseCache.
 */
class PremiseCache
{
    /** @var string */
    protected $key;

    /**
     * @var int
     */
    protected static $ttlMinutes = 60;

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getCache(string $key)
    {
        $this->key = $key;

        $result = Cache::get($key);

        return $result === null ? false : $result;
    }

    /**
     * @param string $value
     */
    public function persistCache(string $value)
    {
        Cache::add($this->key, $value, self::$ttlMinutes);
    }

    /**
     * @param string $key
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function RemovePathFromCache(string $key)
    {
        if ($this->getCache($key)) {
            Cache::delete($key);
        }
    }

    /**
     * @return string
     * @param  mixed  $authId
     * @param  mixed  $thredId
     * @param  mixed  $replyId
     */
    public function cacheKeyForActivity($authId, $thredId, $replyId): string
    {
        $key = $authId.':'.$thredId.':'.$replyId;

        return $key;
    }
}
