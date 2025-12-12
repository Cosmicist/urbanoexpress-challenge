<?php

namespace Module\Shared\Infra;

use Module\Shared\Infra\Http\Middleware\ExceptionMiddleware;
use Module\Shared\Infra\Http\Middleware\JsonResponseMiddleware;
use Module\Shipping\Infra\ShippingModuleBootstrap;
use Slim\App;

class AppBootstrap implements ModuleBootstrap {
	public function bootstrap(App $app): void {
		$app->addRoutingMiddleware();
		$app->addBodyParsingMiddleware();
		$app->add(ExceptionMiddleware::class);
		$app->add(JsonResponseMiddleware::class);

		/** @var ModuleBootstrap[] */
		$modules = [
			$app->getContainer()->get(ShippingModuleBootstrap::class)
		];

		foreach ($modules as $module) {
			$module->bootstrap($app);
		}
	}
}
