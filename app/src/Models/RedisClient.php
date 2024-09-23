<?php

namespace Models;

class RedisClient
{
    private $client;

    // 建構函式，初始化 Redis 連線
    public function __construct()
    {
        try {
            $this->getConnection();
        } catch (\Exception $err) {
            print_err("[RedisClient] [" . $err->getMessage() . "]");
        };
    }

    // 檢查 Redis 是否已連線
    public function isConnected()
    {
        return $this->client != null;
    }

    // 取得 Redis 連線
    private function getConnection()
    {
        if ($this->client != null) {
            return;
        };

        try {
            $host     = (string) ($_ENV['REDIS_HOST']     ?? 'localhost');
            $port     = (int)    ($_ENV['REDIS_PORT']     ?? 6379);
            $password = (string) ($_ENV['REDIS_PASSWORD'] ?? '');
            $options  = [
                'host' => $host,
                'port' => $port,
                'persistent' => true
            ];

            if (!empty($password)) {
                $options['password'] = $password;
            };

            $this->client = new \Predis\Client($options); // 初始化 Redis 連線
            $this->client->select(0); // 選擇資料庫 0
            $this->client->connect(); // 連線至 Redis
        } catch (\Exception $err) {
            print_err("[RedisClient] [" . $err->getMessage() . "]");
            $this->client = null; // 若連線失敗，設置客戶端為 null
        };
    }

    // 從 Redis 取得資料
    public function get($db, $key)
    {
        try {
            $this->getConnection();
            if ($this->client !== null) {
                $this->client->select($db); // 選擇指定的資料庫
                $result = $this->client->get($key); // 取得指定鍵的值
                return $result;
            }
            return null;
        } catch (\Exception $err) {
            http_response_code(500);
            print_err($err->getMessage());
            return null;
        }
    }

    // 將資料寫入 Redis
    public function set($db, $key, $content, $expire)
    {
        try {
            $this->getConnection();
            if ($this->client !== null) {
                $this->client->select($db); // 選擇指定的資料庫
                $this->client->set($key, $content); // 設置指定鍵的值
                $this->client->expire($key, $expire); // 設置鍵的過期時間
            }
        } catch (\Exception $err) {
            http_response_code(500);
            print_err($err->getMessage());
        }
    }

    // 解構函式，關閉 Redis 連線
    public function __destruct()
    {
        if ($this->client !== null && $this->client->ping()) {
            $this->client->disconnect();
            $this->client = null;
        };
    }
}
