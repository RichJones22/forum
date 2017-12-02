<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Favorite;
use App\Reply;

/**
 * Class FavoritesController.
 */
class FavoritesController extends Controller
{
    /**
     * @var Favorite
     */
    private $favorite;

    /**
     * FavoritesController constructor.
     *
     * @param Favorite $favorite
     */
    public function __construct(Favorite $favorite)
    {
        $this->middleWare('auth');
        $this->setFavorite($favorite);
    }

    /**
     * @param Reply $reply
     */
    public function show(Reply $reply)
    {
        $reply->favorite();
    }

    /**
     * @return Favorite
     */
    public function getFavorite(): Favorite
    {
        return $this->favorite;
    }

    /**
     * @param Favorite $favorite
     *
     * @return FavoritesController
     */
    public function setFavorite(Favorite $favorite): FavoritesController
    {
        $this->favorite = $favorite;

        return $this;
    }
}
