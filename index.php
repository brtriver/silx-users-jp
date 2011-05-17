<?php
// 1-1. phar が利用できる環境の場合は silex.phar を読み込み
require_once __DIR__.'/silex.phar';
// 1-2. phar が利用できない環境の場合は ソースを直接読み込み
//require_once __DIR__.'/Silex.git/autoload.php';

$app = new Silex\Application();

$app->register(new Silex\Extension\TwigExtension(), array(
    'twig.path'       => __DIR__.'/views',
    'twig.class_path' => __DIR__.'/vendor/Twig.git/lib',
));

// Hello Your Name
$app->get('/hello/{name}', function ($name) {
    return "Hello $name. Welcome to Silex Users JP!";
});

// index
$app->get('/', function () use($app) {
    return $app['twig']->render('index.twig');
});

$app->run();