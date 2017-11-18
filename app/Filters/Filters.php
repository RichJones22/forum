<?php

declare(strict_types=1);

namespace App\Filters;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Class Filters.
 */
abstract class Filters
{
    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Builder
     */
    protected $builder;
    /**
     * @var User
     */
    protected $user;

    /**
     * ThreadFilters constructor.
     *
     * @param Request $request
     * @param User    $user
     */
    public function __construct(Request $request, User $user)
    {
        $this->setRequest($request);
        $this->setUser($user);
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     *
     * @return Filters
     */
    public function setRequest(Request $request): Filters
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return Filters
     */
    public function setUser(User $user): Filters
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Builder
     */
    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    /**
     * @param mixed $builder
     *
     * @return Filters
     */
    public function setBuilder($builder): Filters
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function apply(Builder $builder)
    {
        $this->setBuilder($builder);

        foreach ($this->getFilters() as $filter => $value) {
            if (method_exists($this, $filter)) {
                $this->$filter($value);
            }
        }

        return $this->getBuilder();
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->getRequest()->only($this->filters);
    }
}
