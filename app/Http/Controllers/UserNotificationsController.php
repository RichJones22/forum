<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\User;

class UserNotificationsController extends Controller
{
    /**
     * UserNotificationsController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function index()
    {
        /** @var User $user */
        $user = auth()->user();
        $user = $user->unreadNotifications();

        return $user->get();
    }

    /**
     * @param User $user
     * @param $notificationId
     */
    public function destroy(User $user, $notificationId)
    {
        /** @var User $user */
        $user = auth()->user();
        $user = $user->notifications();

        $user->findOrFail($notificationId)->get()->markAsRead();
    }
}
