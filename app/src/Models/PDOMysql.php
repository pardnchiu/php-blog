<?php

namespace Models;

class PDOMysql
{
    private $client;

    // 建構函式，初始化 MySQL 連線
    public function __construct($target = "READ")
    {
        try {
            $this->getConnection($target);
        } catch (\Exception $err) {
            http_response_code(500);
            print_err("[MysqlClient] [DB_" . $target . "] [" . $err->getMessage() . "]");
        };
    }

    // 取得 MySQL 連線
    private function getConnection($target)
    {
        if (isset($this->client)) {
            return;
        };

        $host     = (string) $_ENV["DB_{$target}_HOST"]     ?? "localhost";
        $port     = (int)    $_ENV["DB_{$target}_PORT"]     ?? 3306;
        $user     = (string) $_ENV["DB_{$target}_USER"]     ?? "root";
        $password = (string) $_ENV["DB_{$target}_PASSWORD"] ?? "";
        $database = (string) $_ENV["DB_{$target}_DATABASE"] ?? "database";
        $charset  = (string) $_ENV["DB_{$target}_CHARSET"]  ?? "utf8mb4";

        $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=$charset";

        try {
            $this->client = new \PDO($dsn, $user, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_PERSISTENT => true
            ]);

            $timeout = 600;

            // 設定等待超時
            $this->client->query("SET SESSION wait_timeout = $timeout");
            // 設定互動超時
            $this->client->query("SET SESSION interactive_timeout = $timeout");
        } catch (\Exception $e) {
            throw new \Exception("[" . $e->getMessage() . "]");
        }
    }

    // 執行 SQL 查詢
    public function query($query, $params = [])
    {
        if (!isset($this->client)) {
            throw new \Exception("[no_database]");
        };

        try {
            $stmt = $this->client->prepare($query);

            if ($params) {
                $index = 1;
                foreach ($params as &$val) {
                    if (is_int($val)) {
                        $stmt->bindValue($index, $val, \PDO::PARAM_INT);
                    } elseif (is_bool($val)) {
                        $stmt->bindValue($index, $val, \PDO::PARAM_BOOL);
                    } elseif (is_null($val)) {
                        $stmt->bindValue($index, $val, \PDO::PARAM_NULL);
                    } else {
                        $stmt->bindValue($index, $val, \PDO::PARAM_STR);
                    }
                    $index++;
                }
            }

            $start = microtime(true) * 1000;
            $stmt->execute();
            $end = microtime(true) * 1000;
            $ms = number_format($end - $start, 2);

            if ($ms > 20) {
                print_dev("[" . $ms . "ms] [" . $query . "]");
            };

            if (stripos($query, 'UPDATE') === 0 || stripos($query, 'INSERT') === 0) {
                return [
                    'insert_id' => $this->client->lastInsertId(),
                    'affected_rows' => $stmt->rowCount()
                ];
            };

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            http_response_code(500);
            print_err("[MysqlClient] [" . $e->getMessage() . "]");

            return null;
        };
    }

    // 解構函式，關閉資料庫連線
    public function __destruct()
    {
        $this->client = null;
    }
}
