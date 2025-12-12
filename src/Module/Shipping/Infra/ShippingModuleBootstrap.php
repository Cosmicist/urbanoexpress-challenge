<?php

namespace Module\Shipping\Infra;

use DI\Container;
use Doctrine\ORM\EntityManager;
use Module\Shared\Infra\Http\Middleware\RequestValidator;
use Module\Shared\Infra\ModuleBootstrap;
use Module\Shipping\Domain\Order;
use Module\Shipping\Domain\OrderRepository;
use Module\Shipping\Infra\Http\OrderCreateController;
use Module\Shipping\Infra\Http\OrderGetController;
use Module\Shipping\Infra\Http\OrderListController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

/** @implements ModuleBootstrap<Container> */
final class ShippingModuleBootstrap implements ModuleBootstrap {
	public function bootstrap(App $app): void {
		$this->defineRoutes($app);
		$this->declareDependencies($app);
	}

	private function defineRoutes(App $app): void {
		$app->group('/orders', function (RouteCollectorProxy $group) {
			$group->get('', OrderListController::class);

			$group->post('', OrderCreateController::class)
						->add(new RequestValidator(bodySchema: $this->getOrderCreateValidationSchema()));

			$group->get('/{orderId}', OrderGetController::class)
						->add(new RequestValidator(routeSchema: $this->getOrderGetValidationSchema()));
		});
	}

	private function getOrderCreateValidationSchema(): string {
		return require __DIR__ . '/Http/validation/orderCreateRequestSchema.php';
	}

	private function getOrderGetValidationSchema(): string {
		return require __DIR__ . '/Http/validation/orderGetRequestSchema.php';
	}

	/** @param App<Container> $app */
	private function declareDependencies(App $app): void {
		$app->getContainer()->set(
			OrderRepository::class,
			fn (Container $container) => $container->get(EntityManager::class)->getRepository(Order::class)
		);
	}
}
