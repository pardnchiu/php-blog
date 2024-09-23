<section class="topic">
    <strong>主題</strong>
    <?php
    foreach ($article_topic as $topic => $count) {
        if ($topic == "") {
            continue;
        };

        $title = htmlspecialchars((string) $topic);
        $total = htmlspecialchars((int) $count);

        echo <<<HTML
        <a href="">{$title} ({$total})</a>
        HTML;
    };
    ?>
</section>