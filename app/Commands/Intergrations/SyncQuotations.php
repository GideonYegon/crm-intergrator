<?php

namespace App\Commands\Intergrations;

use App\Services\HanaDBConnector;
use App\Support\CRMServiceProvider;
use Illuminate\Support\Facades\Log;
use App\Services\ConfigProviderService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class SyncQuotations extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'app:sync-quotations';

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

            $url = "/api/get-quotations";

            
            $configs = (new ConfigProviderService())->configs($clientID);
            $databaseName = $configs['CompanyDB'];
            $this->comment("------------------DATABASE: " . $databaseName);
           
            $pdo = HanaDBConnector::get()->connect($clientID);

            dd($pdo);

          

            $quotations = (new CRMServiceProvider())->getData($url, $clientID);


            // dd($response);

       
            $responses = $quotations;
         

      
            if (count($responses) <= 0) {
                Log::error("NO PENDING QUOTATIONS TO PROCESS, DATABASE: " . count($responses));
                continue;
            }

         

            foreach ($responses as $key => $response) {

                dd($response);

                $expressWayUpdateApprovalLogUrl = "/update-approval-decison/" . $decision['request_id'];
                try {
                    $dataParams = [
                        "ApprovalRequestDecisions" => [
                            [
                                "Status" => $decision['Status'],
                                "Remarks" => $decision['Remarks'],
                                "ApproverUserName" => $decision['ApproverUserName'],
                                "ApproverPassword" => $decision['ApproverPassword']
                            ]
                        ]
                    ];



                    $url = env('SAP_SERVICE_LAYER_URL') . "/b1s/v1/ApprovalRequests({$decision['WddCode']})";

                    $response =   (new SAPServiceLayerProvider())->postDataToServiceLayer($clientID, $url, $dataParams);

               
                    $data = [
                        'SyncStatus' => 1,
                        'OtherInfo' => "Approved Sucessfully",
                        'sync_on' => now()
                    ];

                    (new SAPExpressWayServiceProvider())->postData($expressWayUpdateApprovalLogUrl, $data);
                } catch (\Throwable $th) {
                    Log::info($th->getMessage());
                    $data = [
                        'SyncStatus' => 2,
                        'OtherInfo' => $th->getMessage(),
                        'sync_on' => now()
                    ];

                    Log::info($expressWayUpdateApprovalLogUrl);
                    (new SAPExpressWayServiceProvider())->postData($expressWayUpdateApprovalLogUrl, $data);
                    continue;
                }
            }
       
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
