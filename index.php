<?php
// 1-1. phar が利用できる環境の場合は silex.phar を読み込み
require_once __DIR__.'/silex.phar';
// 1-2. phar が利用できない環境の場合は ソースを直接読み込み
//require_once __DIR__.'/Silex.git/autoload.php';

// 2. use で追加で利用する名前空間の指定。ここではエラー処理をerrorメソッドで行っているので追加
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// 3. Silexのアプリケーションを作成
$app = new Silex\Application();

// 4. アプリケーションにエクステンションを登録
$app->register(new Silex\Extension\TwigExtension(), array(
    'twig.path'       => __DIR__.'/views',
    'twig.class_path' => __DIR__.'/vendor/Twig/lib',
));
$app->register(new Silex\Extension\UrlGeneratorExtension());

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