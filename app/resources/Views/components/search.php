<section class="search">
    <label>
        <?php
        if (preg_match("/^\/a\/[\w\-\_\.]+/", $_SERVER['REQUEST_URI'])) {
            echo <<<HTML
            <input type="text" placeholder="輸入內文">
            HTML;
        } else {
            echo <<<HTML
            <input type="text" placeholder="輸入關鍵字">
            HTML;
        };
        ?>
    </label>
    <i class="fa-solid fa-magnifying-glass"></i>
</section>