<?php

namespace App\Console;

use App\Models\AppSetting;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Cache;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\DailySummaryReminder'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->everyMinute();
        $appSetting = Cache::remember('appsetting',86400,function(){
            return AppSetting::first();
        });

        //08:00
        if ($appSetting->email_report ?? false)
            $schedule->command('daily:report')->dailyAt($appSetting->email_time);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {

        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
