<?php

/** @noinspection SpellCheckingInspection */

declare(strict_types=1);

namespace tests\Unit;

use App\Domain\Spam\Spam;
use Tests\TestCase;

class SpamTest extends TestCase
{
    /** @test
     * @throws \Exception
     */
    public function it_checks_for_validates_keywords()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('Innocent reply here.'));

        $this->expectException('Exception');

        $spam->detect('yahoo Customer Support');
    }

    /** @test
     * @throws \Exception
     */
    public function it_checks_for_any_key_being_held_down()
    {
        $spam = new Spam();

        $this->expectException('Exception');

        $spam->detect('Hello world aaaaaaaaaaa');
    }
}
