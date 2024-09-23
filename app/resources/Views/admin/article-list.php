<!DOCTYPE html>
<html lang="zh-Hans-TW">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="no">
    <meta name="apple-mobile-web-app-capable" content="no">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css" as="style" crossorigin>
    <link rel="preload" href="https://cdn.jsdelivr.net/gh/pardnchiu/web-management/css/index.css" as="style" crossorigin>
    <link rel="preload" href="https://cdn.jsdelivr.net/gh/pardnchiu/PDRenderKit@1.3.2/dist/PDRenderKit.js" as="script" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/pardnchiu/web-management/css/index.css">
    <script src="https://cdn.jsdelivr.net/gh/pardnchiu/PDRenderKit@1.3.2/dist/PDRenderKit.js" copyright="Pardn Ltd"></script>

    <script src="/js/admin.min.js"></script>
</head>

<body id="body">
    <!-- 登入 -->
    <section class="login dom-temp" :if="is_guest">
        <h1>{{ login.title }}</h1>
        <section>
            <label>
                <input type="password" placeholder="API Key" :model="key">
            </label>
            <i class="fa-solid fa-eye-slash" @click="show"></i>
        </section>
        <button @click="login">
            前往
            <i class="fa-solid fa-arrow-right"></i>
        </button>
    </section>
    <section class="dom-temp" :else>
        <!-- 頂部導覽列 -->
        <?php include 'components/body-top.php'; ?>
        <!-- 左側導覽列 -->
        <?php include 'components/body-left.php'; ?>
        <section class="body-right">
            <!-- 頂部導覽列 -->
            <nav>
                <button @click="body_left_show">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <a href="/admin">首頁</a>
                <a href="/admin/article/list">文章列表</a>
            </nav>
            <!--  -->
            <section class="body-right-database">
                <section>
                    <!-- 列表 -->
                    <table>
                        <thead>
                            <tr>
                                <?php
                                foreach ($article_list_key as $key => $value) {
                                    echo <<<HTML
                                    <th>
                                        {$value}
                                        <i class="fa-solid fa-caret-up"></i>
                                    </th>
                                    HTML;
                                };
                                ?>
                                <th>其他</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($article_list as $data) {
                                $sn    = (string) $data["sn"];
                                $state = (string) $data["state"];

                                echo "<tr>";

                                foreach ($article_list_key as $key => $value) {
                                    $body = $data[$key];
                                    echo "<td>{$body}</td>";
                                };

                                if (preg_match('/尚未發布/', $state)) {
                                    echo <<<HTML
                                    <td>
                                        <button data-sn="{$sn}" @click="editData" >編輯</button>
                                        <button data-sn="{$sn}" @click="setPublic" >發布</button>
                                        <button data-sn="{$sn}" @click="deleteData" class="alert">刪除</button>
                                    </td>
                                    HTML;
                                } elseif (preg_match('/已發布/', $state)) {
                                    echo <<<HTML
                                    <td>
                                        <button data-sn="{$sn}" @click="editData" >編輯</button>
                                        <button data-sn="{$sn}" @click="setPrivate" >下架</button>
                                    </td>
                                    HTML;
                                } elseif (preg_match('/已下架/', $state)) {
                                    echo <<<HTML
                                    <td>
                                        <button data-sn="{$sn}" @click="editData" >編輯</button>
                                        <button data-sn="{$sn}" @click="setPublic" >上架</button>
                                        <button data-sn="{$sn}" @click="deleteData" class="alert">刪除</button>
                                    </td>
                                    HTML;
                                };

                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </section>
                <!-- 按鈕 -->
                <footer>
                    <section>
                        <a href="" user-select="0">上一頁</a>
                        <a href="" user-select="0">下一頁</a>
                    </section>
                    <section>
                        <p user-select="0">顯示</p>
                        <select name="" id="">
                            <option value="">10</option>
                            <option value="">50</option>
                        </select>
                        <p user-select="0">結果</p>
                    </section>
                </footer>
            </section>
        </section>
    </section>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            page = new PD({
                id: "body",
                data: {
                    is_guest: false,
                    login: {
                        title: "LOGIN",
                        email: "",
                        password: "",
                    },
                    title: "管理後台",
                    left: {
                        is_body_left_min: $cookie("is_body_left_min"),
                        is_database_list: 0,
                        is_database_add: 0,
                        is_article_add: 0,
                        is_folder_image: 0,
                        is_file_edit: 0,
                        is_json_edit: 0
                    },
                },
                event: {
                    ...default_events,
                    editData: function(e) {
                        const _this = e.target;
                        const sn = _this.dataset.sn;

                        location.href = "/admin/article/edit/" + sn;
                    }
                },
                next: () => {}
            });
        });
    </script>
</body>

</html>