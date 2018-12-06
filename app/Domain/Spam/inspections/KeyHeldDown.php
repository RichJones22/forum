<?php

declare(strict_types=1);

namespace App\Domain\Spam\inspections;

use App\Domain\Spam\Contract\InspectionsContract;
use Exception;

class KeyHeldDown implements InspectionsContract
{
    /**
     * @param string $body
     *
     * @return mixed|void
     *
     * @throws Exception
     */
    public function detect(string $body)
    {
        if (preg_match('/(.)\\1{4,}/', $body)) {
            throw new Exception('your reply contains spam');
        }
    }
}
