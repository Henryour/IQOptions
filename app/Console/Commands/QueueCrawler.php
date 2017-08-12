<?php

namespace App\Console\Commands;

use App\AMQP;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Mookofe\Tail\Tail;

class QueueCrawler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run consumer on queue with numbers';

    /**
     * Execute the console command.
     *
     * return void
     */
    public function handle()
    {
        (new Tail())->listen(AMQP::QUEUE_NAME_NUMBERS,  function ($message) {
            //@todo: disable auto ack. Make ack and nack functions
            DB::table('raw_numbers')->insert([
                ['number' => $message],
            ]);
        });
    }
}
