<section class="post desktop">
    <strong>最新文章</strong>
    <?php
    foreach ($article_new as $row) {
        $uri             = htmlspecialchars((string) $row['uri']);
        $title           = htmlspecialchars((string) $row['title']);
        $upload          = (int) $row['upload'];
        $upload_date     = htmlspecialchars(date("Y-m-d\TH:i:sP", $upload));
        $upload_text     = htmlspecialchars(date("Y/m/d H:i", $upload));
        $watch           = htmlspecialchars((int) $row['watch']);

        echo <<<HTML
        <a href="/a/{$uri}">
            <strong>{$title}</strong>
            <p>
                <time datetime="{$upload_date}">{$upload_text}</time>・
                <i class="fa-solid fa-eye"></i> {$watch}
            </p>
        </a>
        HTML;
    };
    ?>
</section>