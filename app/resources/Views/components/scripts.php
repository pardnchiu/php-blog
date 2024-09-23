<?php
$JS_VERSION = 1717725212;

if (preg_match("/^\/a\/[\w\-\_\.]+/", $_SERVER['REQUEST_URI'])) {
    echo <<<HTML
    <link rel="preload" href="https://cdn.jsdelivr.net/gh/pardnchiu/PDRenderKit@1.3.2/dist/PDRenderKit.js" as="script" crossorigin>
    <script src="https://cdn.jsdelivr.net/gh/pardnchiu/PDRenderKit@1.3.2/dist/PDRenderKit.js" copyright="Pardn Ltd"></script>
    <script type="module" src="/js/article.min.js?v={$JS_VERSION}" defer></script>
    HTML;
} else {
    echo <<<HTML
    <link rel="preload" href="https://cdn.jsdelivr.net/gh/pardnchiu/PDRenderKit@1.3.2/dist/PDRenderKit.js" as="script" crossorigin>
    <script src="https://cdn.jsdelivr.net/gh/pardnchiu/PDRenderKit@1.3.2/dist/PDRenderKit.js" copyright="Pardn Ltd"></script>
    <script src="/js/index.min.js?v={$JS_VERSION}" defer></script>
    HTML;
};
