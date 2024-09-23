<?php

namespace AdminControllers;

use Models\Async;

class ArticleAddController
{
    private static string   $view           = "admin/article-add";
    private static string   $root           = "";
    private static string   $page_key       = "";
    private static int      $page           = 0;
    private static int      $page_row       = 50;
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
        $this->mysqlClient = new \Models\PDOMysql("READ");
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
        $tasks = [];

        return Async::run($tasks)
            ->then(function ($result) {
                return $this->render($result);
            })
            ->catch(function ($err) {
                throw new \Exception($err->getMessage());
            });
    }

    private function render($result)
    {
        // 將數組鍵值對轉換為局部變數
        $article_list_key = [
            "sn" => "編號",
            "uri" => "uri",
            "title" => "標題",
            "subtitle" => "副標題",
            "seo_title" => "SEO標題",
            "seo_description" => "SEO描述",
            "watch" => "觀看次數",
            "upload" => "上傳日期",
            "dismiss" => "狀態"
        ];
        extract($result);

        $HEAD = json_decode(<<<JSON
        {
            "robots": "index follow",
            "title": "個人創作者",
            "description": "個人創作者描述",
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
