<?php

namespace Module\Shared\Infra;

use Psr\Container\ContainerInterface;
use Slim\App;

/**
 * @template TContainer of ContainerInterface
 */
interface ModuleBootstrap {
	/**
	* @param App<TContainer> $app
	*/
	public function bootstrap(App $app): void;
}
