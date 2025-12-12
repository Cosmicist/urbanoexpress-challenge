<?php

namespace Module\Shipping\Infra;

use Module\Shared\Infra\Http\Middleware\RequestValidator;
use Module\Shared\Infra\ModuleBootstrap;
use Module\Shipping\Infra\Http\OrderCreateController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

final class ShippingModuleBootstrap implements ModuleBootstrap {
	public function bootstrap(App $app): void {
		$app->group('/orders', function (RouteCollectorProxy $group) {
			$group->post('', OrderCreateController::class)
						->add(new RequestValidator(bodySchema: $this->getOrderCreateValidationSchema()));
		});
	}

	private function getOrderCreateValidationSchema(): string {
		return require __DIR__ . '/Http/validation/orderCreateRequestSchema.php';
	}
}
