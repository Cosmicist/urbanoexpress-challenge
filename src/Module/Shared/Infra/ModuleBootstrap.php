<?php

namespace Module\Shared\Infra;

use Psr\Container\ContainerInterface;
use Slim\App;

interface ModuleBootstrap {
	/**
	* @param App<ContainerInterface> $app
	*/
	public function bootstrap(App $app): void;
}
