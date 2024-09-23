<?php

namespace Models;

class SessionClient
{
    // 建構函式，初始化 session 設定
    public function __construct($ROOT, $REDIS_CLIENT = null)
    {
        ini_set('display_errors', 1); // 顯示錯誤
        ini_set('display_startup_errors', 1); // 啟動時顯示錯誤
        ini_set('session.cookie_lifetime', 86400 * 7); // 設定 cookie 期限為 7 天
        ini_set('session.gc_maxlifetime', 86400 * 7); // 設定 session 垃圾回收最大存活時間為 7 天
        error_reporting(E_ALL); // 錯誤報告級別設為 E_ALL

        if ($REDIS_CLIENT->isConnected()) {
            $host     = (string) $_ENV['REDIS_HOST']     ?? 'localhost';
            $port     = (int)    $_ENV['REDIS_PORT']     ?? 6379;
            $password = (string) $_ENV['REDIS_PASSWORD'] ?? '';
            $uri      = "tcp://{$host}:{$port}?auth={$password}&database=0&persistent=1";

            ini_set('session.save_handler', 'redis'); // 設定 session 儲存處理器為 Redis
            ini_set('session.save_path', $uri); // 設定 session 儲存路徑為 Redis URI
        } else {
            $folder = $ROOT . '/storage/sessions'; // 設定 session 儲存資料夾

            // 資料夾不存在也無法新增
            if (!is_dir($folder) && !mkdir($folder, 0777, true)) {
                return;
            };

            // 資料夾無法寫入
            if (!is_writable($folder)) {
                return;
            };

            ini_set('session.save_handler', 'files'); // 設定 session 儲存處理器為檔案
            ini_set('session.save_path', $folder); // 設定 session 儲存路徑為資料夾
        };

        // 若 session 尚未啟動，啟動 session
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        };
    }

    // 設定 session 值
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    // 取得 session 值
    public function get($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    // 刪除 session 值
    public function delete($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        };
    }

    // 銷毀 session
    public function destroy()
    {
        session_destroy();
    }

    // 重新生成 session ID
    public function regenerateId()
    {
        session_regenerate_id(true);
    }

    // 取得 session ID
    public function getId()
    {
        return session_id();
    }

    // 取得 session 建立時間
    public function getCreatedTime()
    {
        if (!isset($_SESSION['created_time'])) {
            $_SESSION['created_time'] = time();
        };
        return $_SESSION['created_time'];
    }
}
