<?php

declare(strict_types=1);

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class ThreadFilters.
 */
class ThreadFilters extends Filters
{
    /**
     * @var array
     */
    protected $filters = ['by'];

    /**
     * @param $username
     *
     * @return Builder
     *
     * @internal param Builder $builder
     */
    public function by($username)
    {
        $user = $this
            ->getUser()
            ->newQuery()
            ->where('name', $username)
            ->firstOrFail();

        return $this->getBuilder()->where('user_id', $user->id);
    }
}
