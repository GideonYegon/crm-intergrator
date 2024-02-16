<?php

namespace App\QueryManager;


use PDO;
use Illuminate\Support\Facades\Log;


class QueryExcuter
{


    public $queryString;
    public $pdo;
    /**
     * init the object with a \PDO object
     * @param type $pdo
     * @param string $queryString Query
     */
    public function __construct(\PDO $pdo, string $queryString)
    {
        $this->pdo = $pdo;
        $this->queryString = $queryString;
    }

    public function fetchData(): array|null
    {
        try {
            $stmt = $this->pdo->prepare($this->queryString);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (\Throwable $th) {
            Log::error("Unable to execute query :" .  $this->queryString);
            throw $th;
        }
    }
}
