<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CRMServiceProvider
{

    /**
     *
     * Post Data
     *
     */
    public function getAccessToken()
    {

        $fullUrl = env('CRM_BASEURL') . '/api/login';
        $userName = env('CRM_USERNAME');
        $password = env('CRM_PASSWORD');
        // $token = env('CRM_TOKEN');


        $data = [
            'email' => $userName,
            'password' => $password,
        ];

  

        $response = Http::withoutVerifying()
            ->acceptJson()
            ->asForm()
            ->retry(2)
            ->post($fullUrl, $data);
   
    //    dd($response->body());

        if ($response->failed()) {
            Log::info("Failed Posting to following URL: " . $fullUrl);
            Log::info(json_encode($response->body()));
            exit;
        }


        Cache::put('CRM_TOKEN', $response['access_token']);
        $token = Cache::get('CRM_TOKEN');

        // dd($token);

        return;


        // Cache::put('CRM_TOKEN', $response);
        // $newtoken = Cache::get('CRM_TOKEN');

        // dd($newtoken);
        
        return;


    }


    /**
     *
     * Post Data
     *
     */
    public function postData(string $apiUrl, array $data)
    {


        
        if (!Cache::has('SAP_EXPRESS_WAY_TOKEN')) {
            Log::info($apiUrl);
            Log::info("Access Token Not Found");
            $this->getAccessToken();
          
        }

        $token = Cache::get('SAP_EXPRESS_WAY_TOKEN');


        $fullUrl = env('SAP_EXPRESS_WAY_BASEURL') . $apiUrl;
        

        $response = Http::withoutVerifying()
            ->connectTimeout(35)
            ->acceptJson()
            ->retry(5)
            ->withToken($token)
            ->post($fullUrl, $data);

        if ($response->unauthorized()) {

            Log::info($apiUrl);
            Log::info("Auth Not Legit");
            $this->getAccessToken();
            $response =  $this->postData($apiUrl, $data);
        }

 
        if ($response->failed()) {
            Log::info("Failed Posting to following URL: " . $apiUrl);
            Log::info(json_encode($response->body()));
        }

      
    }

    /**
     *
     * Post Data
     *
     */
    public function getData(string $apiUrl)
    {

        

        $token = Cache::get('CRM_TOKEN');
        if (!$token) {
            $this->getAccessToken();
        }    

        $fullUrl = env('CRM_BASEURL') . $apiUrl;
        

        $response = Http::withoutVerifying()
            ->connectTimeout(15)
            ->acceptJson()
            ->withToken($token)
            ->get($fullUrl);

        $quotations = json_decode($response->body());


        if ($response->unauthorized()) {
            $this->getAccessToken();
            $response =  $this->getData($apiUrl);
        }
        if ($response->failed()) {
            Log::info("Failed Posting to following URL: " . $apiUrl);
            Log::info(json_encode($response->body()));
        }

        return $quotations;
    }
}
