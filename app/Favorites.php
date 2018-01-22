<?php

declare(strict_types=1);

namespace App;

/**
 * Trait Favorites.
 */
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

    public function unfavorite()
    {
        $attributes = ['user_id' => auth()->id()];

        $this->favorites()->where($attributes)->delete();
    }

    /**
     * @return bool
     */
    public function isFavorited()
    {
        return (bool) $this->favorites->where('user_id', auth()->id())->count();
    }

    /**
     * @return bool
     */
    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }

    /**
     * @return int
     */
    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }
}
