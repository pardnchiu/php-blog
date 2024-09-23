<?php

namespace Models;

class MysqlClient
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

        // 初始化 MySQL 連線
        $this->client = new \mysqli('p:' . $host, $user, $password, $database, $port);

        // 連線錯誤
        if ($this->client->connect_error) {
            throw new \Exception("[connect_error] [" . $this->client->connect_error . "]");
        };

        // 設定字元集錯誤
        if (!$this->client->set_charset($charset)) {
            throw new \Exception("[set_charset] [" . $this->client->error . "]");
        };

        // 設定選項錯誤
        if (!$this->client->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10)) {
            throw new \Exception("[options] [" . $this->client->error . "]");
        };

        $timeout = 600;

        // 設定等待超時錯誤
        if (!$this->client->query("SET SESSION wait_timeout = $timeout")) {
            throw new \Exception("[wait_timeout] [" . $this->client->error . "]");
        };

        // 設定互動超時錯誤
        if (!$this->client->query("SET SESSION interactive_timeout = $timeout")) {
            throw new \Exception("[interactive_timeout] [" . $this->client->error . "]");
        };
    }

    // 執行 SQL 查詢
    public function query($query, $params = [])
    {
        // 沒有資料庫連線
        if (!isset($this->client)) {
            throw new \Exception("[no_database]");
        };

        try {
            // 準備 SQL 查詢
            $stmt = $this->client->prepare($query);

            // 準備查詢錯誤
            if ($stmt == false) {
                throw new \Exception("[prepare] [" . $this->client->error . "]");
            };

            if ($params) {
                $types = '';

                foreach ($params as $param) {
                    if (is_int($param)) {
                        $types .= 'i';
                    } elseif (is_float($param) || is_double($param)) {
                        $types .= 'd';
                    } elseif (is_string($param)) {
                        $types .= 's';
                    } else {
                        $types .= 'b';
                    };
                };

                // 綁定參數
                $stmt->bind_param($types, ...$params);
            };

            $start = microtime(true) * 1000;
            $stmt->execute();
            $end = microtime(true) * 1000;
            $ms = number_format($end - $start, 2);

            if ($ms > 20) {
                print_dev("[" . $ms . "ms] [" . $query . "]");
            };

            // 查詢錯誤
            if ($this->client->error) {
                throw new \Exception("[" . $this->client->error . "]");
            };

            if (stripos($query, 'UPDATE') === 0 || stripos($query, 'INSERT') === 0) {
                return [
                    'insert_id' => $this->client->insert_id,
                    'affected_rows' => $stmt->affected_rows
                ];
            };

            $data = [];
            $result = $stmt->get_result(); // 獲取查詢結果

            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            };

            // 關閉查詢
            $stmt->close();

            return $data;
        } catch (\Exception $err) {
            http_response_code(500);
            print_err("[MysqlClient] [" . $err->getMessage() . "]");

            // 關閉查詢
            $stmt->close();

            return null;
        };
    }

    // 解構函式，關閉資料庫連線
    public function __destruct()
    {
        if ($this->client) {
            $this->client->close();
            $this->client = null;
        }
    }
}
