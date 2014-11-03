<?php
// 1. autoloader
require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// 2. Silexのアプリケーションを作成
$app = new Silex\Application();

// 3. アプリケーションにエクステンションを登録
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// 5-1. ユーザーガイドのTOPページを表示
//      '/' にGETメソッドででアクセスされた場合にこの部分が処理されます。
$app->get('/', function () use($app) {
    return $app['twig']->render('index.twig');
})
->bind('top');

// 5-2. ソースをハイライト表示
//      '/view/' にGETメソッドでアクセスされた場合にこの部分を処理
//      このルーティングに'view_source' と名前を付けて、　テンプレートからは　{{app['url_generator'].generate('view_source')}}　でURLの文字列を取得
$app->get('/view/', function () use($app) {
    return highlight_file(__FILE__, true);
})
->bind('view_source');

// 6. エラー(例外)が発生したときの処理
$app->error(function (\Exception $e) use ($app) {
    $error = null;
    if ($e instanceof NotFoundHttpException || in_array($app['request']->server->get('REMOTE_ADDR'), array('127.0.0.1', '::1'))) {
        $error = $e->getMessage();
    }
    return new Response(
        $app['twig']->render('error.twig', array('error' => $error)),
        $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500
    );
});

$app->run();