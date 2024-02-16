<?php

namespace App\Services;

class ConfigProviderService
{

    public function configs(int $clientID)
    {

        $setupData =  config('app.setting_file');

        $settingJsonString = file_get_contents($setupData);
        $settingsData = json_decode($settingJsonString, true);

        $dbSettings = collect($settingsData['DATABASES']);

        $currentDbSettings = $dbSettings->filter(function ($doc, $key) use ($clientID) {
            return $doc['CLIENT_ID'] == $clientID;
        });

        $currentDbSettingsData = $currentDbSettings->first();

        $CompanyDB = $currentDbSettingsData['DB_NAME'];
        $ODBC_CONNECTION = $currentDbSettingsData['ODBC_CONNECTION'];
        $SAP_DB_SERVER_TYPE = $currentDbSettingsData['SetupData']['SAP_DB_SERVER_TYPE'];
        $DB_HOST = $currentDbSettingsData['SetupData']['DB_HOST'];
        $LICENSE_SERVER = $currentDbSettingsData['SetupData']['LICENSE_SERVER'];
        $SLD_SERVER = $currentDbSettingsData['SetupData']['SLD_SERVER'];
        $SAP_USERNAME = $currentDbSettingsData['SetupData']['USERNAME'];
        $SAP_PASSWORD = $currentDbSettingsData['SetupData']['PASSWORD'];
        $DB_USERNAME = $currentDbSettingsData['SetupData']['DB_USERNAME'];
        $DB_PASSWORD = $currentDbSettingsData['SetupData']['DB_PASSWORD'];
        // $draftQueryName = $currentDbSettingsData['SetupData']['DRAFT_QUERY_NAME'];


        $sapConfig = [
            "server" => $DB_HOST,
            "DbServerType" => $SAP_DB_SERVER_TYPE,
            "LicenseServer" =>   $LICENSE_SERVER,
            "SLDServer" =>  $SLD_SERVER,
            "CompanyDB" =>   $CompanyDB,
            "DbUserName" => $DB_USERNAME,
            "DbPassword" => $DB_PASSWORD,
            "username" => $SAP_USERNAME,
            "password" => $SAP_PASSWORD,
            'ODBC_CONNECTION' => $ODBC_CONNECTION,
            // 'draftQueryName' => $draftQueryName
        ];


        return $sapConfig;
    }
}
