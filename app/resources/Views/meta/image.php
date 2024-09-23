<?php

$HEAD_IMAGE = $HEAD["image"] ?? null;

if (!isset($HEAD_IMAGE)) {
    return;
};

echo <<<HTML
<meta property="og:image" content="{$HEAD_IMAGE}"/>
<meta property="twitter:image" content="{$HEAD_IMAGE}"/>
HTML;
