<?php

$HEAD_ROBOTS = $HEAD['robots'] ?? "index follow";

echo <<<HTML
<meta name="robots" content="{$HEAD_ROBOTS}"/>
<meta name="googlebot" content="{$HEAD_ROBOTS}"/>
HTML;
