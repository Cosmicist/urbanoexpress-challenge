<?php

use DI\Bridge\Slim\Bridge;
use Module\Shared\Infra\Http\Middleware\ExceptionMiddleware;

require_once __DIR__ . '/../../vendor/autoload.php';

$container = require __DIR__ . '/container.php';

$app = Bridge::create($container);
$app->add(ExceptionMiddleware::class);
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

return $app;
