<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        \App\Console\Commands\GenerateBotOrder::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $orderSettings = \App\Models\OrderBotSettings::first();

        if ($orderSettings && $orderSettings->order_frequency) {
            $frequency = strtolower($orderSettings->order_frequency);

            switch ($frequency) {
                case 'second':
                    // Laravel's scheduler doesn't support per-second execution natively
                    // You'd need to use a separate script or cron job for real-time second intervals
                    break;

                case 'minute':
                    $schedule->command('generate-bot-order')->everyMinute();
                    break;

                case 'hourly':
                    $schedule->command('generate-bot-order')->hourly();
                    break;

                case 'day':
                    $schedule->command('generate-bot-order')->daily();
                    break;

                default:
                    break;
            }
        }
    }


    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
