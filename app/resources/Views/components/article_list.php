<section class="article-list">
    <?php
    foreach ($article_list as $row) {
        $uri             = htmlspecialchars((string) $row['uri']);
        $title           = htmlspecialchars((string) $row['title']);
        $seo_description = htmlspecialchars((string) $row['seo_description']);
        $upload          = (int) $row['upload'];
        $upload_date     = htmlspecialchars(date("Y-m-d\TH:i:sP", $upload));
        $upload_text     = htmlspecialchars(date("Y/m/d H:i", $upload));
        $watch           = htmlspecialchars((int) $row['watch']);
        $charge          = (int) $row['charge'];
        $is_buy          = (int) $row['is_buy'];

        if ($charge === 1 && $is_buy === 1) {
            echo <<<HTML
            <a href="/a/{$uri}">
                <strong> 
                    <i class="fa-solid fa-lock-open"></i>
                    {$title}
                </strong>
                <p>
                    <time datetime="{$upload_date}">{$upload_text}</time>・
                    <i class="fa-solid fa-eye"></i> {$watch}
                </p>
                <p>{$seo_description}</p>
            </a>
            HTML;
        } else if ($charge === 1 && $is_buy !== 1) {
            echo <<<HTML
            <button data-uri="{$uri}">
                <strong>
                    <i class="fa-solid fa-lock"></i>
                    {$title}
                </strong>
                <p>
                    <time datetime="{$upload_date}">{$upload_text}</time>・
                    <i class="fa-solid fa-eye"></i> {$watch}
                </p>
                <p>{$seo_description}</p>
            </button>
            HTML;
        } else {
            echo <<<HTML
            <a href="/a/{$uri}">
                <strong>{$title}</strong>
                <p>
                    <time datetime="{$upload_date}">{$upload_text}</time>・
                    <i class="fa-solid fa-eye"></i> {$watch}
                </p>
                <p>{$seo_description}</p>
            </a>
            HTML;
        };
    };
    ?>
</section>
<?php
$href_pre = $article_page['href_pre'] ?? "";
$href_next = $article_page['href_next'] ?? "";

echo <<<HTML
<section class="page">
    <a href="{$href_pre}">上一頁</a>
    <a href="{$href_next}">下一頁</a>
</section>
HTML;
?>