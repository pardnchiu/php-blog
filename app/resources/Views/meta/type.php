<?php

$HEAD_TYPE = $HEAD["type"] ?? "website";

echo <<<HTML
<meta property="og:type" content="{$HEAD_TYPE}"/>
HTML;
