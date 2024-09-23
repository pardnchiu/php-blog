<?php

$HEAD_REFERRER = $HEAD["referrer"] ?? "strict-origin-when-cross-origin";

echo <<<HTML
<meta name="referrer" content="{$HEAD_REFERRER}"/>
HTML;
