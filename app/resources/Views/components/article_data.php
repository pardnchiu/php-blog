<?php
$title       = htmlspecialchars((string) $article_data['title']);
$subtitle    = htmlspecialchars((string) $article_data['subtitle']);
$watch       = htmlspecialchars((int) $article_data['watch']);
$upload      = (int) $article_data['upload'];
$upload_date = htmlspecialchars(date("Y-m-d\TH:i:sP", $upload));
$upload_text = htmlspecialchars(date("Y/m/d H:i", $upload));

echo <<<HTML
<h1 class="dom-temp">{$title}</h1>
<h2 class="dom-temp">{$subtitle}</h2>
<p>
    <time datetime="{$upload_date}">{$upload_text}</time>・
    <i class="fa-solid fa-eye"></i> <span name="watch-total">{$watch}</span>
</p>
<details class="heading">
    <summary>索引</summary>
</details>
<section class="body"></section>
HTML;
?>
<section class="page around">
    <?php
    if ($article_pre != null) {
        echo <<<HTML
            <a href="{$article_pre}" name="上一篇標題">上一篇</a>
            HTML;
    } else {
        echo <<<HTML
            <span></span>
            HTML;
    };

    if ($article_next != null) {
        echo <<<HTML
            <a href="{$article_next}" name="下一篇標題">下一篇</a>
            HTML;
    };
    ?>
</section>