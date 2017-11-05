<?php declare(strict_types=1);
/**
 * @param $class
 * @param array $attributes
 *
 * @return mixed
 */
function create($class, $attributes = [])
{
    return factory($class)->create($attributes);
}

/**
 * @param $class
 * @param array $attributes
 *
 * @return mixed
 */
function make($class, $attributes = [])
{
    return factory($class)->make($attributes);
}
