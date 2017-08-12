<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class JsonbReader extends Command
{
    protected $answerPattern = 'New numbers: %s' . PHP_EOL;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jsonb:read';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read new JSONB objects from jsonb_numbers';


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        while(true) {
            $readNumbers = DB::select('UPDATE jsonb_numbers SET is_new = ? WHERE is_new = ? RETURNING numbers', [false, true]);
            isset(reset($readNumbers)->numbers) && print(sprintf($this->answerPattern, join(', ', array_map(function($item) { return $item->numbers; }, $readNumbers))));
            usleep(500);
        }
    }
}
