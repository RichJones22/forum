<?php

declare(strict_types=1);
use Illuminate\Database\Seeder;

/**
 * Class DatabaseSeeder.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        if (app()->environment() === 'local') {
            $this->call(RunTinkerLoadCommand::class);
        }
    }
}
