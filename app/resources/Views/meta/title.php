<?php

$HEAD_TITLE = $HEAD['title'] ?? "";

echo <<<HTML
<title>{$HEAD_TITLE}</title>
<meta property="og:title" content="{$HEAD_TITLE}"/>
<meta property="twitter:title" content="{$HEAD_TITLE}"/>
HTML;
