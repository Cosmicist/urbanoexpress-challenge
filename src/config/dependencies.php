<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Module\Shared\Infra\Http\Middleware\ExceptionMiddleware;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Error\Renderers\JsonErrorRenderer;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

return [
	'settings' => fn() => require __DIR__ . '/settings.php',

	LoggerInterface::class => function (ContainerInterface $c) {
		$settings = $c->get('settings')['logger'];

		$rotatingFileHandler = new RotatingFileHandler($settings['path'], 0, Logger::toMonologLevel($settings['level']));
		$rotatingFileHandler->setFormatter(new LineFormatter(null, null, false, true));

		$logger = new Logger($settings['name']);
		$logger->pushHandler($rotatingFileHandler);

		return $logger;
	},

	ExceptionMiddleware::class => function (ContainerInterface $c) {
		$settings = $c->get('settings');

		return new ExceptionMiddleware(
			$c->get(JsonErrorRenderer::class),
			$c->get(LoggerInterface::class),
			(bool) $settings['displayErrorDetails']
		);
	},

	EntityManagerInterface::class => fn (ContainerInterface $c) => $c->get(EntityManager::class),

	EntityManager::class => function (ContainerInterface $c) {
		$settings = $c->get('settings')['doctrine'];

		foreach ($settings['types'] as $name => $class) {
			if(!Type::hasType($name)) {
				Type::addType($name, $class);
			}
		}

		$cache = $settings['dev_mode']
			? new ArrayAdapter()
			: new FilesystemAdapter(directory: $settings['cache_dir'], defaultLifetime: 0);

		$config = ORMSetup::createAttributeMetadataConfig(
			paths: $settings['metadata_dirs'],
			isDevMode: $settings['dev_mode'],
			cache: $cache,
		);
		$config->enableNativeLazyObjects(true);

		$connection = DriverManager::getConnection($settings['connection']);

		return new EntityManager($connection, $config);
	},
];
