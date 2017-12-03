<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Reply.
 */
class Reply extends Model
{
    /**
     * @var array
     */
    protected $localAttributes;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * Reply constructor.
     *
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
        return $this->favorites()->where($this->localAttributes)->exists();
    }
}
