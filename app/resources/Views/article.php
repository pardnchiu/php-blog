<!DOCTYPE html>
<html lang="zh-Hans-TW">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="no">
    <meta name="apple-mobile-web-app-capable" content="no">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="manifest" href="/manifest.json">
    <!--  -->
    <?php include 'meta/index.php'; ?>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <?php include 'components/styles.php'; ?>
    <?php include 'components/scripts.php'; ?>
    <!--  -->
    <style>
        :root {
            --width-max: 1024px;

            --col-brand-1: #F5F5F5;
            --col-brand-2: #C7C7C7;
            --col-brand-3: #999999;
            --col-brand-4: #6B6B6B;
            --col-brand-5: #f1c50e;
            --col-brand-6: #303030;
            --col-brand-7: #242424;
            --col-brand-8: #171717;
            --col-brand-9: #0A0A0A;

            --gradient-col: linear-gradient(to right bottom, rgb(70, 180, 105), rgb(70, 130, 180), rgb(250, 75, 75));
        }
    </style>
    <?php
    // 将数组编码为 JSON
    $jsonData = json_encode($article_data);

    // 对 JSON 数据进行 URL 编码
    $encodedJsonData = rawurlencode($jsonData);

    // 生成 <link> 标签
    echo '<link href="data:application/json,' . $encodedJsonData . '" as="fetch" id="json_data">';
    ?>
</head>

<body id="body">
    <!--  -->
    <?php include 'components/nav.php'; ?>
    <!--  -->
    <section id="blog">
        <section>
            <!-- 左側內容 -->
            <section class="left">
                <!--  -->
                <?php include 'components/header.php'; ?>
                <!--  -->
                <?php include 'components/article_data.php'; ?>
            </section>
            <!-- 右側選單 -->
            <section class="right">
                <!--  -->
                <?php include 'components/header.php'; ?>
                <!--  -->
                <?php include 'components/search.php'; ?>
                <!--  -->
                <?php include 'components/article_tag.php'; ?>
            </section>
        </section>
    </section>
    <!--  -->
    <?php include 'components/banner-bottom.php'; ?>
    <!--  -->
    <section class="float-bottom">
        <?php include 'components/search.php'; ?>
    </section>
    <!--  -->
    <?php include 'components/footer.php'; ?>
</body>

</html>