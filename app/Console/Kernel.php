<?php

namespace App\Console;

use App\Models\OrderBotSettings;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        \App\Console\Commands\GenerateBotOrder::class,
    ];

    protected function shouldRunCronJob(): bool
    {
        $botStatus = \App\Models\OrderBotSettings::select('shopify_bot')->first();

        return $botStatus ? (bool)$botStatus->shopify_bot : false;
    }


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
                    $schedule->command('generate-bot-order')
                        ->everyMinute()
                        ->when(function () {
                            return $this->shouldRunCronJob(); // Check if the cron should run
                        });
                    break;

                case 'minute':
                    $schedule->command('generate-bot-order')
                        ->everyMinute()
                        ->when(function () {
                            return $this->shouldRunCronJob();
                        });
                    break;

                case 'hourly':
                    $schedule->command('generate-bot-order')
                        ->hourly()
                        ->when(function () {
                            return $this->shouldRunCronJob(); // Check if the cron should run
                        });
                    break;

                case 'day':
                    $schedule->command('generate-bot-order')
                        ->daily()
                        ->when(function () {
                            return $this->shouldRunCronJob(); // Check if the cron should run
                        });
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
