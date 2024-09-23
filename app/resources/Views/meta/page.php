<?php

$HEAD_PAGE = $HEAD["page"];

if (!isset($HEAD_PAGE)) {
    return;
};

$HEAD_PAGE_HOME = $HEAD_PAGE['home'];
$HEAD_PAGE_PREV = $HEAD_PAGE['prev'];
$HEAD_PAGE_NEXT = $HEAD_PAGE['next'];

if ($HEAD_PAGE_HOME != null) {
    echo <<<HTML
    <link rel="home" href="{$HEAD_PAGE_HOME}"/>
    HTML;
};

if ($HEAD_PAGE_PREV != null) {
    echo <<<HTML
    <link rel="prev" href="{$HEAD_PAGE_PREV}"/>
    HTML;
};

if ($HEAD_PAGE_NEXT != null) {
    echo <<<HTML
    <link rel="next" href="{$HEAD_PAGE_NEXT}"/>
    HTML;
};
