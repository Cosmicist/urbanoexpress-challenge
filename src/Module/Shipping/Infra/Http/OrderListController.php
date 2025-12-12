<?php

namespace Module\Shipping\Infra\Http;

use Module\Shipping\Application\UseCase\ListOrders\ListOrdersUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class OrderListController {
	public function __construct(private ListOrdersUseCase $listOrdersUseCase) {}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response) {
		$customerId = 'foo-customer-id'; // TODO: Get user from auth context and filter orders by user

		$orders = $this->listOrdersUseCase->execute($customerId);

		$response->getBody()->write(json_encode(['orders' => $orders]));
		return $response;
	}
}
