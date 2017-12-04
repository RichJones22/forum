<?php declare(strict_types=1);

namespace App;

/**
 * Trait Favorites.
 */
trait Favorites
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorited');
    }

    public function favorite()
    {
        if ( ! $this->favorites()->where($this->localAttributes)->exists()) {
            $this->favorites()->create($this->localAttributes);
        }
    }

    /**
     * @return bool
     */
    public function isFavorited()
    {
        return (bool) $this->favorites->where('user_id', auth()->id())->count();
    }

    /**
     * @return int
     */
    public function getFavoritesCount()
    {
        return $this->favorites->count();
    }
}
