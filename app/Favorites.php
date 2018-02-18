<?php

declare(strict_types=1);

namespace App;

/*
 * Trait Favorites.
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

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

    /**
     * @throws \ReflectionException
     */
    public function favorite()
    {
        if ( ! $this->favorites()->where($this->localAttributes)->exists()) {
            $this->favorites()->create($this->localAttributes);

            $this->recordActivity('created');
        }
    }

    public function unfavorite()
    {
        $attributes = ['user_id' => auth()->id()];

        /** @var Collection<Model> $models */
        $models = $this->favorites()->where($attributes)->get();

        /* @var Model $model */
        try {
            foreach ($models as $model) {
                // delete favorite
                $model->delete();

                /** @var MorphMany $morphMany */
                $morphMany = $model->activity();

                /** @var Model $related */
                $related = $morphMany->getRelated();

                $activities = $related
                    ->newQuery()
                    ->where(['subject_id' => $model->getAttribute('id')])
                    ->get();

                // delete activity
                foreach ($activities as $activity) {
                    $activity->delete();
                }
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * @return bool
     */
    public function isFavorited()
    {
        return (bool) $this->favorites()->where('user_id', auth()->id())->count();
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
        return $this->favorites()->count();
    }
}
