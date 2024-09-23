<?php

$web = [
    '/'       => Controllers\HomeController::class,
    '/a/:uri' => Controllers\ArticleController::class,
];

$admin = [
    '/admin'                   => AdminControllers\ArticleListController::class,
    '/admin/article/list'      => AdminControllers\ArticleListController::class,
    '/admin/article/add'       => AdminControllers\ArticleAddController::class,
    '/admin/article/edit/:uri' => AdminControllers\ArticleEditController::class,
    '/admin/folder/image'      => AdminControllers\ArticleListController::class,
    '/admin/banner/top'        => AdminControllers\ArticleListController::class,
    '/admin/banner/bottom'     => AdminControllers\ArticleListController::class,
];

return [
    ...$web,
    ...$admin
];
