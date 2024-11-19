<?php

namespace App\Console\Commands;

use App\Services\OrderGenerate;
use Illuminate\Console\Command;

class GenerateBotOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate-bot-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Generate bot Orders on Shopify';

    /**
     * The Order Generation Service.
     *
     * @var string
     */
    protected $orderGenerate;

    public function __construct(OrderGenerate $orderGenerate)
    {
        parent::__construct(); // Call the parent constructor
        $this->orderGenerate = $orderGenerate;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderSettings = \App\Models\OrderBotSettings::first();
        $frequency = strtolower($orderSettings->order_frequency);

        if ($frequency == 'second') {
            for ($i = 0; $i < 60; $i++) {
                $this->orderGenerate->generateOrders();
                sleep(1);
            }
        } else {

            $this->orderGenerate->generateOrders();
        }
    }


}
