<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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
        return view('profiles.show', [
            'profileUser' => $user,
            'threads' => $user->threads()->paginate(10),
        ]);
    }
}
