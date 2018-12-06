<?php

declare(strict_types=1);

namespace App\Domain\Spam\Inspections;

use App\Domain\Spam\Contract\InspectionsContract;
use Exception;

class InvalidKeywords implements InspectionsContract
{
    protected $invalidKeywords = [
        'yahoo customer support',
    ];

    /**
     * @param string $body
     *
     * @return mixed|void
     *
     * @throws Exception
     */
    public function detect(string $body)
    {
        foreach ($this->invalidKeywords as $keyword) {
            if (false !== stripos($body, $keyword)) {
                throw new Exception('your reply contains spam');
            }
        }
    }
}
