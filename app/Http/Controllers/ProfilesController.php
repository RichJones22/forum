<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Collection;

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
            'activities' => $this->getActivity($user),
        ]);
    }

    /**
     * @param User $user
     *
     * @return Collection
     */
    protected function getActivity(User $user): Collection
    {
        return $user
            ->activity()
            ->latest()
            ->with('subject')
            ->take(50)
            ->get()
            ->groupBy(function ($x) {
                return $x->created_at->format('Y-m-d');
            });
    }
}
