<?php
require_once __DIR__.'/silex.phar';

$app = new Silex\Application();

// Hello Your Name
$app->get('/hello/{name}', function ($name) {
    return "Hello $name. Welcome to Silex Users JP!";
});

// index
$app->get('/', function () {
    return "This site is Silex Users JP.";
});

$app->run();