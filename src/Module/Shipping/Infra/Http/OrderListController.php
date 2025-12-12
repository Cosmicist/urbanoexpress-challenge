<?php

namespace Module\Shipping\Infra\Http;

use Module\Shipping\Application\UseCase\ListOrders\ListOrdersUseCase;
use Module\User\Domain\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class OrderListController {
	public function __construct(private ListOrdersUseCase $listOrdersUseCase) {}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response) {
		/** @var User */
		$user = $request->getAttribute('user');

		$orders = $this->listOrdersUseCase->execute($user->id);

		$response->getBody()->write(json_encode(['orders' => $orders]));
		return $response;
	}
}
