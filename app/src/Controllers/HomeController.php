<?php

namespace Controllers;

use Models\Async;
use Models\MysqlClient;
use Models\PDOMysql;

class HomeController
{
    private static string   $view           = "home";
    private static string   $root           = "";
    private static string   $page_key       = "";
    private static int      $page           = 0;
    private static int      $page_row       = 10;
    private static int      $page_max       = 0;
    private static string   $topic          = "";
    private static string   $tag            = "";
    private static bool     $isRenew        = false;

    private $mysqlClient = null;
    private $cacheClient = null;

    public function __construct()
    {
        global $ROOT, $CACHE_CLIENT;

        self::$root     = (string)  $ROOT;
        self::$page_key = (string)  $_SERVER['REQUEST_URI'];
        self::$page     = (int)     (isset($_GET['page'])   ? trim($_GET['page'])   : 0);
        self::$topic    = (string)  (isset($_GET['topic'])  ? trim($_GET['topic'])  : "");
        self::$tag      = (string)  (isset($_GET['tag'])    ? trim($_GET['tag'])    : "");
        self::$isRenew  = (bool)    (isset($_GET['renew']) && $_GET['renew'] === "1");

        $this->cacheClient = $CACHE_CLIENT;
        $this->mysqlClient = new PDOMysql("READ");
    }

    public function init()
    {
        $cache = self::$isRenew ? null : $this->cacheClient->get(self::$page_key);

        if ($cache == null) {
            try {
                // 生成內容
                $this->view()
                    ->then(function ($content) {
                        // 添加生成的內容至快取
                        $content = $this->cacheClient->set(self::$page_key, $content, 0);
                        // 回傳生成的內容
                        echo $content;
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
        echo $cache;
    }

    protected function view()
    {
        $tasks = [
            'article_list' => [
                "method" => fn () => $this->getArticleList()
            ],
            'article_page' => [
                "method" => fn () => $this->getArticlePage(),
                'tasks' => ['article_list']
            ],
            'article_new' => [
                "method" => fn () => $this->getArticleNew()
            ],
            'article_topic' => [
                "method" => fn () => $this->getArticleTopic()
            ],
            'article_tag' => [
                "method" => fn () => $this->getArticleTag()
            ]
        ];

        return Async::run($tasks)
            ->then(function ($result) {
                return $this->renderView($result);
            })
            ->catch(function ($err) {
                throw new \Exception($err->getMessage());
            });
    }

    public function getArticleList()
    {

        $ary_topic = array_filter(explode(",", self::$topic), function ($e) {
            return strlen($e) > 0;
        });

        if (empty($ary_topic)) {
            $filter_topic = "";
        } else {
            $conditions = array_map(function ($e) {
                return "LOCATE(LOWER(?), LOWER(article.topic)) > 0";
            }, $ary_topic);

            $filter_topic = "AND (" . implode(" OR ", $conditions) . ")";
        };

        $ary_tag = array_filter(explode(",", self::$tag), function ($e) {
            return strlen($e) > 0;
        });

        if (empty($ary_tag)) {
            $filter_tag = "";
        } else {
            $conditions = array_map(function ($e) {
                return "LOCATE(LOWER(?), LOWER(article.tag)) > 0";
            }, $ary_tag);

            $filter_tag = "AND (" . implode(" OR ", $conditions) . ")";
        };

        $query = <<<MYSQL
        SELECT
            COUNT(*) OVER() AS total,
            data.*
        FROM (
            SELECT
                article.sn,
                article.uri,
                article.title,
                LENGTH(article.content) AS content_length,
                article.seo_description,
                article.watch,
                article.upload,
                article.charge,
                IF (article_buy.dismiss = 0, 1, 0) AS is_buy
            FROM article
                LEFT JOIN article_buy
                    ON article_buy.article_sn = article.sn
                    AND article_buy.user_sn = 1
            WHERE article.dismiss = 0
                AND article.state = 1
                {$filter_topic}
                {$filter_tag}
        ) AS data
        ORDER BY data.sn DESC
        LIMIT ?
        OFFSET ?
        MYSQL;
        $result = $this->mysqlClient->query($query, [
            ...$ary_topic,
            ...$ary_tag,
            self::$page_row,
            self::$page * self::$page_row,
        ]);

        if (empty($result)) {
            return [];
        };


        $total = (int) $result[0]["total"];
        self::$page_max = ceil($total / self::$page_row) - 1;

        return $result;
    }

    public function getArticlePage()
    {
        $page_pre   = max(0, self::$page - 1);
        $page_next  = self::$page + 1 > self::$page_max ? self::$page_max : self::$page + 1;

        $query_params = $_GET;

        $query_params['page'] = $page_pre;
        $href_pre = http_build_query($query_params);

        $query_params['page'] = $page_next;
        $href_next = http_build_query($query_params);

        return array(
            "href_pre"  => self::$page == 0 ? "javascript:alert('已在第一頁')" : "/?$href_pre",
            "href_next" => self::$page == self::$page_max ? "javascript:alert('已在最後一頁')" : "/?$href_next"
        );
    }

    public function getArticleNew()
    {
        $query = <<<MYSQL
        SELECT
            uri,
            title,
            seo_description,
            watch,
            upload
        FROM article
        WHERE dismiss = 0
        AND state = 1
        ORDER BY sn DESC
        LIMIT 10
        OFFSET 0
        MYSQL;
        $result = $this->mysqlClient->query($query);

        return $result;
    }

    public function getArticleTopic()
    {
        $query = <<<MYSQL
        SELECT
            GROUP_CONCAT(
                article.topic
                ORDER BY article.topic
                SEPARATOR ','
            ) AS topic
        FROM article
        WHERE dismiss = 0
            AND state = 1
            AND topic <> 'empty'
        MYSQL;
        $result = $this->mysqlClient->query($query);

        if (empty($result)) {
            return [];
        };

        $e = $result[0];

        return array_reduce(explode(',', $e['topic']), function ($obj, $item) {
            if (!isset($obj[$item])) {
                $obj[$item] = 0;
            };

            $obj[$item]++;

            return $obj;
        }, []);
    }

    public function getArticleTag()
    {
        $result = $this->mysqlClient->query(<<<MYSQL
        SELECT
            GROUP_CONCAT(
                DISTINCT article.tag
                ORDER BY article.tag
                SEPARATOR ','
            ) AS tag

        FROM article

        WHERE dismiss = 0
            AND state = 1
            AND tag <> 'empty'
        MYSQL, []);

        if (empty($result)) {
            return [];
        };

        $e = $result[0];

        return array_reduce(explode(',',  $e['tag']), function ($obj, $item) {
            if (!isset($ary[$item])) {
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
                "prev": null,
                "next": null
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
