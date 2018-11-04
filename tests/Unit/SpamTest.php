<?php

namespace tests\Unit;
use Tests\TestCase;
use App\Domain\Spam\Spam;

class SpamTest extends TestCase
{
    /** @test
     * @throws \Exception
     */
    public function it_validates_spam()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('Innocent reply here.'));
    }
}
