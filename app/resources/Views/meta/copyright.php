<?php

$HEAD_COPYRIGHT = $HEAD["copyright"];

if (!isset($HEAD_COPYRIGHT)) {
    return;
};

echo <<<HTML
<meta name="copyright" content="{$HEAD_COPYRIGHT}"/>
HTML;
