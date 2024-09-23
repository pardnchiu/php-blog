<section class="tag">
    <strong>標籤</strong>
    <?php
    foreach ($article_tag as $tag => $count) {
        if ($tag == "") {
            continue;
        };

        $title = htmlspecialchars($tag);

        echo <<<HTML
        <a href="/?tag={$title}">{$title}</a>
        HTML;
    };
    ?>
</section>