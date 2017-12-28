<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class Activity.
 */
class Activity extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * @param User $user
     * @param int  $take
     *
     * @return Collection
     */
    public function feed(User $user, int $take = 50): Collection
    {
        return $user
            ->activity()
            ->latest()
            ->with('subject')
            ->take($take)
            ->get()
            ->groupBy(function ($x) {
                return $x->created_at->format('Y-m-d');
            });
    }
}
