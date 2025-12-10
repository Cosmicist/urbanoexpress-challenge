<?php

use DI\ContainerBuilder;

require_once __DIR__ . '/../../vendor/autoload.php';

return new ContainerBuilder()
	->addDefinitions(__DIR__ . '/dependencies.php')
	->build();
