<?php

namespace App\Console\Commands;

use App\Jobs\DailyReminder;
use Illuminate\Console\Command;

class DailySummaryReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends daily report to doctors';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        dispatch(new DailyReminder);
        return Command::SUCCESS;
    }
}
