<?php

use Ramsey\Uuid\Doctrine\UuidType;

use function DI\env;

define('SOURCE_ROOT', realpath(__DIR__ . '/..'));

return [
	'displayErrorDetails' => true,
	'logErrors' => true,
	'logErrorDetails' => true,
	'logger' => [
		'name' => 'app',
		'path' => SOURCE_ROOT . '/../var/logs/app.log',
		'level' => \Psr\Log\LogLevel::DEBUG,
	],
	'doctrine' => [
		'dev_mode' => true,
		'cache_dir' => SOURCE_ROOT . '/../var/doctrine',
		'metadata_dirs' => [SOURCE_ROOT . '/Module'],
		'connection' => [
			'driver' => 'pdo_pgsql',
			'host' => $_ENV['DB_HOST'],
			'port' => $_ENV['DB_PORT'],
			'dbname' => $_ENV['DB_NAME'],
			'user' => $_ENV['DB_USER'],
			'password' => $_ENV['DB_PASSWORD'],
		],
		'types' => [
			UuidType::NAME => UuidType::class,
		]
	],
];
