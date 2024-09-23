<?php

$HEAD_THEME = $HEAD["theme"] ?? "#ffffff";

echo <<<HTML
<meta name="theme-color" content="{$HEAD_THEME}"/>
HTML;
