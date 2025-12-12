<?php

use DI\Bridge\Slim\Bridge;
use Module\Shared\Infra\AppBootstrap;

require_once __DIR__ . '/../../vendor/autoload.php';

$container = require __DIR__ . '/container.php';

$app = Bridge::create($container);

// Bootstrap App
$bootstrap = new AppBootstrap();
$bootstrap->bootstrap($app);

return $app;
