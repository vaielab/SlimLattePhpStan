<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use DI\Container;
use DI\ContainerBuilder;
use Latte\Engine;
use Latte\Loaders\FileLoader;
use Slim\Factory\AppFactory;
use Slim\App as SlimApp;

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([


    Engine::class => function () {
        $latte = new Engine();



        $latte->setLoader(new FileLoader(__DIR__ . '/../templates/'));
        $latte->setTempDirectory(__DIR__ . '/../compiledTemplates/');
        $latte->setAutoRefresh(true);

        return $latte;
    },
    



]);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

AppFactory::setContainer($container);

$app = AppFactory::create();
$app->addRoutingMiddleware();

$app->get('/test01', [\App\Controllers\Test01::class, 'index']);
$app->get('/test02', [\App\Controllers\Test02::class, 'index']);
$app->get('/test03', [\App\Controllers\Test03::class, 'index']);


$app->run();
