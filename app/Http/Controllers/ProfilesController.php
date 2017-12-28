<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Activity;
use App\User;

/**
 * Class ProfilesController.
 */
class ProfilesController extends Controller
{
    /**
     * @param User $user
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(User $user)
    {
        /** @var Activity $activity */
        $activity = $user->activity()->getModel();

        return view('profiles.show', [
            'profileUser' => $user,
            'activities' => $activity->feed($user),
        ]);
    }
}
