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
        //
       $cron =  $this->orderGenerate->generateOrders();
//       return  response()->json(['success',$cron]);
    }


}
