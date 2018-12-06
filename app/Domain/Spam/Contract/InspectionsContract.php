<?php

declare(strict_types=1);

namespace App\Domain\Spam\Contract;

interface InspectionsContract
{
    /**
     * @param string $body
     *
     * @return mixed
     */
    public function detect(string $body);
}
