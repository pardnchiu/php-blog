<?php

$HEAD_DESCRIPTION = $HEAD['description'] ?? "";

echo <<<HTML
<meta name="description" content="{$HEAD_DESCRIPTION}"/>
<meta property="og:description" content="{$HEAD_DESCRIPTION}"/>
<meta property="twitter:description" content="{$HEAD_DESCRIPTION}"/>
HTML;
