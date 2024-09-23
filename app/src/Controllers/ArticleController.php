<?php

namespace Controllers;

use Models\Async;
use Models\MysqlClient;

class ArticleController
{
    private static string $view = "article";
    private static string $root = "";
    private static string $page_key = "";
    private static string $uri = "";
    private static string $watch_key = "";
    private static int $watch_expire = 60 * 10;
    private static int $article_sn = 0;
    private static bool $isRenew = false;

    private $mysqlClient = null;
    private $cacheClient = null;
    private $redisClient = null;

    public function __construct()
    {
        global $ROOT, $REDIS_CLIENT, $CACHE_CLIENT;

        self::$root = (string) $ROOT;
        self::$page_key = (string) $_SERVER['REQUEST_URI'];
        self::$isRenew = (bool) (isset($_GET['renew']) && $_GET['renew'] === "1");

        $this->cacheClient = $CACHE_CLIENT;
        $this->redisClient = $REDIS_CLIENT;
        $this->mysqlClient = new MysqlClient("READ");
    }

    public function init($param)
    {
        self::$uri = $param["uri"];
        self::$watch_key = md5(session_id() . "-article-" . self::$uri);
        $cache = self::$isRenew ? null :  $this->cacheClient->get(self::$page_key);

        // 更新觀看次數
        $this->updateWatchTotal();

        if ($cache == null) {
            try {
                // 生成內容
                $this->view()
                    ->then(function ($content) {
                        // 添加生成的內容至快取
                        $content =  $this->cacheClient->set(self::$page_key, $content, 0);
                        // 回傳生成的內容
                        echo $this->replaceContentWatchtotal($content);
                    })
                    ->catch(function ($err) {
                        throw new \Exception($err->getMessage());
                    });
            } catch (\Exception $err) {
                print_err($err->getMessage());
            };
            return;
        };

        // 回傳快取的內容
        echo $this->replaceContentWatchtotal($cache);
    }

    private function updateWatchTotal()
    {
        // Redis不存在則停止動作
        if ($this->redisClient == null) {
            return;
        };

        $record =  $this->redisClient->get(2, self::$watch_key);

        // 觀看紀錄不存在則停止動作
        if ($record != null) {
            return;
        };

        // 寫入觀看紀錄
        $this->redisClient->set(2, self::$watch_key, time(), self::$watch_expire);

        // 更新觀看次數
        $sql = <<<SQL
        UPDATE article
        SET watch = watch + 1
        WHERE uri = ?
        SQL;

        $this->mysqlClient->query($sql, [
            self::$uri
        ]);
    }

    protected function view()
    {
        $tasks = [
            'article_data' => [
                "method" => fn () => $this->getArticleData(),
            ],
            'article_pre' => [
                "method" => fn () => $this->getPreArticleLink(),
                "tasks" => ["article_data"]
            ],
            'article_next' => [
                "method" => fn () => $this->getNextArticleLink(),
                "tasks" => ["article_data"]
            ],
            'article_tag' => [
                "method" => fn () => $this->getArticleTag(),
            ],
        ];

        return Async::run($tasks)
            ->then(function ($result) {
                return $this->renderView($result);
            })
            ->catch(function ($err) {
                throw new \Exception($err->getMessage());
            });
    }

    private function replaceContentWatchtotal($content)
    {
        // 取得觀看次數
        $query = <<<MYSQL
        SELECT watch 
        FROM article 
        WHERE uri = ?
        MYSQL;
        $result = $this->mysqlClient->query($query, [
            self::$uri
        ]);

        $total = empty($result) ? 0 : $result[0]['watch'];

        return preg_replace(
            '/<span name="watch-total">\d+<\/span>/',
            '<span name="watch-total">' . $total . '</span>',
            $content
        );
    }

    private function getArticleData()
    {
        // 取得文章內容
        $query = <<<MYSQL
        SELECT 
        sn,
        uri,
        title,
        subtitle,
        content,
        LENGTH(content) AS content_length,
        seo_title,
        seo_description,
        watch,
        upload
        FROM article 
        WHERE dismiss = 0
        AND state = 1
        AND uri = ?
        MYSQL;
        $result = $this->mysqlClient->query($query, [
            self::$uri
        ]);

        if (empty($result)) {
            return null;
        }

        $e = $result[0];

        self::$article_sn = (int) $e['sn'];

        return $e;
    }

    private function getPreArticleLink()
    {
        // 取得文章內容
        $query = <<<MYSQL
        SELECT 
        uri
        FROM article 
        WHERE sn = (
        SELECT MAX(sn) 
        FROM article 
        WHERE sn < ?
        AND dismiss = 0
        )
        MYSQL;
        $result = $this->mysqlClient->query($query, [
            self::$article_sn
        ]);

        if (empty($result)) {
            return null;
        };

        $e = $result[0];

        $link = "/a/" . $e['uri'];

        return $link;
    }

    private function getNextArticleLink()
    {
        // 取得文章內容
        $sql = <<<MYSQL
        SELECT 
        uri
        FROM article 
        WHERE sn = (
        SELECT MIN(sn) 
        FROM article 
        WHERE sn > ?
        AND dismiss = 0
        )
        MYSQL;
        $result = $this->mysqlClient->query($sql, [
            self::$article_sn
        ]);


        if (empty($result)) {
            return null;
        };

        $e = $result[0];

        $link = "/a/" . $e['uri'];

        return $link;
    }

    private function getArticleTag()
    {
        // 取得文章標籤
        $query = <<<MYSQL
        SELECT tag
        FROM article
        WHERE dismiss = 0
        AND uri = ?
        AND tag <> 'empty'
        MYSQL;
        $result = $this->mysqlClient->query($query, [
            self::$uri
        ]);

        if (empty($result)) {
            return [
                "無標籤" => 0
            ];
        };

        $row = $result[0];
        $tag = $row['tag'];
        $ary = explode(',', $tag);

        return array_reduce($ary, function ($obj, $item) {
            if (!isset($obj[$item])) {
                $obj[$item] = 0;
            };

            $obj[$item]++;

            return $obj;
        }, []);
    }

    private function renderView($result)
    {
        // 將數組鍵值對轉換為局部變數
        extract($result);

        $HEAD = json_decode(<<<JSON
        {
            "robots": "index follow",
            "title": "Debian / 1c2g / Apache / PHP / Mariadb",
            "description": "Debian / 1c2g / Apache / PHP / Mariadb",
            "image": null,
            "icon": null,
            "theme": null,
            "sitename": null,
            "type": "website",
            "author": {
                "name": null,
                "url": null,
                "published": null,
                "modified": null
            },
            "page": {
                "home": "https://demo-blog.pardn.io",
                "prev": "{$article_pre}",
                "next": "{$article_next}"
            },
            "canonical": null,
            "referrer": "strict-origin-when-cross-origin",
            "copyright": null
        }
        JSON, true);

        // 開啟輸出緩衝區
        ob_start();
        // 包含視圖文件
        include self::$root . "/resources/Views/" . self::$view . ".php";
        // 返回視圖內容
        return ob_get_clean();
    }
}
