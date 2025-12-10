<?php

use Dotenv\Dotenv;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;

require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

/** @var App */
$app = require __DIR__ . '/../config/bootstrap.php';

$app->get('/', function (Response $response) {
	$response->getBody()->write(json_encode(['status' => 'error', 'message' => 'Nothing to see here, move along.']));
	return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
});

$app->get('/fail', function () {
	throw new \Exception("Simulated exception for testing.");
});

$app->run();
