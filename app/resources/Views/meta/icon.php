<?php

$HEAD_ICON = $HEAD["icon"] ?? null;

if (!isset($HEAD_ICON)) {
    return;
};

echo <<<HTML
<link rel="icon" type="image/x-icon" href="{$HEAD_ICON}"/>
<link rel="apple-touch-icon" href="{$HEAD_ICON}"/>
<link rel="apple-touch-icon-precomposed" href="{$HEAD_ICON}"/>
HTML;
