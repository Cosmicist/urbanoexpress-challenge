#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use DI\Container;
use Dotenv\Dotenv;
use Module\User\Application\UseCases\CreateUserUseCase;
use Module\User\Domain\Service\AccessTokenGenerator;
use Symfony\Component\Console\Application;
use Module\User\Infra\Command\CreateUserCommand;
use Module\User\Infra\Security\SimpleAccessTokenGenerator;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

/** @var Container */
$container = require __DIR__ . '/src/config/container.php';
$container->set(AccessTokenGenerator::class, fn () => new SimpleAccessTokenGenerator());

$command = new CreateUserCommand();
$command->init($container->get(CreateUserUseCase::class));

$application = new Application();
$application->addCommand($command);
$application->run();
