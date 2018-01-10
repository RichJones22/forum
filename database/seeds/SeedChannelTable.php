<?php declare(strict_types=1);
use App\Channel;
use Illuminate\Database\Seeder;

class SeedChannelTable extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        factory(Channel::class, 10)->create();
    }
}
