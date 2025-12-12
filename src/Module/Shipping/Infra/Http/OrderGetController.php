<?php

namespace Module\Shipping\Infra\Http;

use Module\Shipping\Application\UseCase\GetOrder\GetOrderUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class OrderGetController {
	public function __construct(private GetOrderUseCase $getOrderUseCase) {}

	public function __invoke(string $orderId, ResponseInterface $response) {
		$customerId = 'foo-customer-id'; // TODO: Get user from auth context and filter orders by user

		$order = $this->getOrderUseCase->execute($orderId, $customerId);
		if ($order === null) {
			$response->getBody()->write(json_encode(['error' => 'Order not found']));
			return $response->withStatus(404);
		}

		$response->getBody()->write(json_encode(['order' => $order]));
		return $response;
	}
}
