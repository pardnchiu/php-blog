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
                <a href="/admin/article/add">文章撰寫</a>
            </nav>
            <!-- 頂部按鈕 -->
            <section class="top-button">
                <section>
                    <button class="hint" title="發布" @click="show_photo_library">
                        <i class="fa-solid fa-file-arrow-up"></i>
                    </button>
                    <button class="hint" title="返回" @click="go_back">
                        <i class="fa-solid fa-reply"></i>
                    </button>
                    <button class="hint" title="前進" @click="go_forward">
                        <i class="fa-solid fa-share"></i>
                    </button>
                    <span></span>
                    <button class="hint" title="H2" @click="add_h2">
                        H<sub>2</sub>
                    </button>
                    <button class="hint" title="H3" @click="add_h3">
                        H<sub>3</sub>
                    </button>
                    <button class="hint" title="H4" @click="add_h4">
                        H<sub>4</sub>
                    </button>
                    <button class="hint" title="H5" @click="add_h5">
                        H<sub>5</sub>
                    </button>
                    <button class="hint" title="粗體" @click="add_bold">
                        <i class="fa-solid fa-bold"></i>
                    </button>
                    <button class="hint" title="斜體" @click="add_italic">
                        <i class="fa-solid fa-italic"></i>
                    </button>
                    <button class="hint" title="刪除線" @click="add_strikethrough">
                        <i class="fa-solid fa-strikethrough"></i>
                    </button>
                    <button class="hint" title="底線" @click="add_underline">
                        <i class="fa-solid fa-underline"></i>
                    </button>
                    <button class="hint" title="標示" @click="add_marker">
                        <i class="fa-solid fa-marker"></i>
                    </button>
                    <button class="hint" title="引用" @click="add_blockquote">
                        <i class="fa-solid fa-quote-left"></i>
                    </button>
                    <button class="hint" title="無序列表" @click="add_ul">
                        <i class="fa-solid fa-list-ul"></i>
                    </button>
                    <button class="hint" title="有序列表" @click="add_ol">
                        <i class="fa-solid fa-list-ol"></i>
                    </button>
                    <button class="hint" title="代碼" @click="add_code">
                        <i class="fa-solid fa-code"></i>
                    </button>
                    <button class="hint" title="連結" @click="add_link">
                        <i class="fa-solid fa-link"></i>
                    </button>
                    <button class="hint" title="圖片庫" @click="show_photo_library">
                        <i class="fa-solid fa-image"></i>
                    </button>
                </section>
            </section>
            <!-- 頂部圖片 -->
            <section class="top-photo">
                <button @click="add_photo" data-src="./image/1-1.jpg">
                    <img src="./image/1-1.jpg" alt="">
                </button>
                <button @click="add_photo" data-src="./image/1-1.jpg">
                    <img src="./image/2-1.jpg" alt="">
                </button>
                <button @click="add_photo" data-src="./image/1-1.jpg">
                    <img src="./image/1-1.jpg" alt="">
                </button>
            </section>
            <!-- 編輯器 -->
            <section class="markdown">
                <!--  -->
                <section class="editor">
                    <!--  -->
                    <section class="top">
                        <label>
                            <textarea class="h1" placeholder="輸入標題"></textarea>
                        </label>
                        <label>
                            <textarea placeholder="輸入副標題"></textarea>
                        </label>
                        <label>
                            <input placeholder="輸入分類"></input>
                        </label>
                        <label>
                            <input placeholder="uri"></input>
                        </label>
                    </section>
                </section>
                <!--  -->
                <section class="viewer"></section>
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
                },
                next: () => {}
            });
        });
    </script>
</body>

</html>