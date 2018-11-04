<?php declare(strict_types=1);

namespace App\Domain\Spam;

use Exception;

class Spam
{
    /**
     * @param $body
     *
     * @return bool
     *
     * @throws Exception
     */
    public function detect($body)
    {
        $this->detectInvalidKeywords($body);

        return false;
    }

    /**
     * @param $body
     *
     * @throws Exception
     */
    public function detectInvalidKeywords($body)
    {
        $invalidKeywords = [
            'yahoo customer support',
        ];

        foreach ($invalidKeywords as $keyword) {
            if (stripos($body, $keyword) !== false) {
                throw new Exception('your reply contains spam');
            }
        }
    }
}
