<?php

declare(strict_types=1);

use App\Reply;
use App\Thread;
use Illuminate\Database\Seeder;

/**
 * Class RunTinkerLoadCommand.
 */
class RunTinkerLoadCommand extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $env = app()->environment();

        if ($env !== 'local') {
            echo"\n";
            echo"Tinker Load Command failed.\n";
            echo"This command only runs in the 'local' environment.\n";
            echo "your environment is '{$env}'.\n";
            echo"\n";

            return;
        }

        // clear the terminal...
        system('clear');

        echo"\n";
        echo "running artisan migrate:refresh";
        echo"\n";

        // refresh the database.
        Artisan::call('migrate:refresh');

        echo"\n";
        echo "seeding the database";
        echo"\n";

        // run tinker load commands to populate the threads, replies, channels, and users tables.
        // please see the factories/UserFactory.php file for the specifics...
        $threads = factory(Thread::class, 50)->create();
        $threads->each(function ($thread) {
            factory(Reply::class, 10)->create(['thread_id' => $thread->id]);
        });

        echo"\n";
        echo"App is ready\n";
        echo"\n";
    }
}
