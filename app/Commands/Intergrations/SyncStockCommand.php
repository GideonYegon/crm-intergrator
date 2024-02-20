<?php

namespace App\Commands\Intergrations;

use App\Services\HanaDBConnector;
use App\QueryManager\QueryExcuter;
use App\Support\CRMServiceProvider;
use App\Services\ConfigProviderService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class SyncStockCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'app:sync-stock-command';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $databases = ['1'];

        foreach ($databases as $key => $clientID) {

            $url = "/stock-info/".$clientID;


            $configs = (new ConfigProviderService())->configs($clientID);
            $databaseName = $configs['CompanyDB'];
            $this->comment("------------------DATABASE: " . $databaseName);
           
            $pdo = HanaDBConnector::get()->connect($clientID);

    
            $queryStringUnsycDocs = "SELECT * FROM \"{$databaseName}\".\"VwStockQuantities\" T0 WHERE T0.\"QtyInstock\" >0";


            $stockData = (new QueryExcuter($pdo, $queryStringUnsycDocs))->fetchData();
            
       
            (new CRMServiceProvider())->postData($url, [
                'stockInfo' => $stockData
            ]);

       
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
