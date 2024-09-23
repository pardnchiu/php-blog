<?php

$HEAD_SITENAME = $HEAD["sitename"];

if (!isset($HEAD_SITENAME)) {
    return;
};

echo <<<HTML
<meta property="og:site_name" content="{$HEAD_SITENAME}"/>
HTML;
