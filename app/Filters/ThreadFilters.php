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
    protected $filters = ['by', 'popular'];

    /**
     * @param $username
     *
     * @return Builder
     *
     * @internal param Builder $builder
     */
    protected function by($username)
    {
        $user = $this
            ->getUser()
            ->newQuery()
            ->where('name', $username)
            ->firstOrFail();

        return $this->getBuilder()->where('user_id', $user->id);
    }

    /**
     * filter by most popular threads...
     *
     * @return mixed
     */
    protected function popular()
    {
        // remove existing order on $builder object, which will clear out existing order by's that have been set.
        $this->getBuilder()->getQuery()->orders = [];

        return $this->getBuilder()->orderby('replies_count', 'desc');
    }
}
