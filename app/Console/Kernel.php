<?php

namespace App\Console;

use App\Models\Kampanya;
use App\Models\Log;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $curDate = date('Y-m-d H:i:s');
            $camps = Kampanya::where([['end_date', '<=', $curDate], ['active', 1]])->get();
            foreach ($camps as $camp) {
                $camp->active = false;
                $camp->save();
                Kampanya::removeCampaignAllProductDiscounts($camp);
            }
        })->everyMinute();
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
