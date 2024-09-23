<?php

$HEAD_CANONICAL = $HEAD["canonical"];

if (!isset($HEAD_CANONICAL)) {
    return;
};

echo <<<HTML
<link rel="canonical" href="{$HEAD_CANONICAL}"/>
HTML;
