<?php declare(strict_types=1);

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Class TestCase.
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @param User|null $user
     *
     * @return $this
     */
    public function signIn(User $user = null)
    {
        $user = $user ?: create(User::class);

        $this->actingAs($user);

        return $this;
    }
}
