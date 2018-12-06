<?php

declare(strict_types=1);

namespace App\Domain\Spam;

use App\Domain\Spam\Contract\InspectionsContract;
use App\Domain\Spam\Inspections\InvalidKeywords;
use App\Domain\Spam\inspections\KeyHeldDown;
use Exception;

class Spam
{
    protected $inspections = [
        InvalidKeywords::class,
        KeyHeldDown::class,
    ];

    /**
     * @param $body
     *
     * @return bool
     *
     * @throws Exception
     */
    public function detect($body)
    {
        foreach ($this->inspections as $inspection) {
            /** @var InspectionsContract $myInspection */
            $myInspection = new $inspection();

            $myInspection->detect($body);
        }

        return false;
    }
}
