<?php

$HEAD_AUTHOR = $HEAD["author"];

if (!isset($HEAD_AUTHOR)) {
    return;
};

$HEAD_AUTH_NAME = $HEAD_AUTHOR["name"];
$HEAD_AUTH_URL = $HEAD_AUTHOR["url"];
$HEAD_AUTHOR_PUBLISHED = $HEAD_AUTHOR["published"];
$HEAD_AUTHOR_MODIFIED = $HEAD_AUTHOR["modified"];

if (isset($HEAD_AUTH_NAME)) {
    echo <<<HTML
    <meta property="author" content="{$HEAD_AUTH_NAME}"/>
    HTML;
};

if (isset($HEAD_AUTH_URL)) {
    echo <<<HTML
    <link rel="author" href="{$HEAD_AUTH_URL}"/>
    HTML;
};

if (isset($HEAD_AUTHOR_PUBLISHED)) {
    echo <<<HTML
    <meta property="article:published_time" content="{HEAD_AUTHOR_PUBLISHED}"/>
    HTML;
};

if (isset($HEAD_AUTHOR_MODIFIED)) {
    echo <<<HTML
    <meta property="article:modified_time" content="{HEAD_AUTHOR_MODIFIED}"/>
    HTML;
};
