<?php

namespace App\Services;

use PDO;

use Illuminate\Support\Facades\Log;
use App\Services\ConfigProviderService;



/**
 * Represent the Connection
 */
class HanaDBConnector
{

    /**
     * Connection
     * @var type
     */
    private static $conn;


    /**
     * Connect to the database and return an instance of \PDO object
     * @return \PDO
     * @throws \Exception
     */
    public function connect(int $clientID): PDO
    {

        $sapConfig = (new ConfigProviderService())->configs($clientID);
        $username =  $sapConfig['DbUserName'];
        $password = $sapConfig['DbPassword'];
        
        $dsn = "odbc:" . $sapConfig['ODBC_CONNECTION'];        

        try {
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (\Throwable $th) {
            Log::error($th);
        }

        // dd($dsn);
    }

    /**
     * return an instance of the Connection object
     * @return type
     */
    public static function get()
    {
        if (null === static::$conn) {
            static::$conn = new static();
        }

        return static::$conn;
    }
}
