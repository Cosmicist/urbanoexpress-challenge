<?php

namespace Module\Shipping\Infra\Http;

use Module\Shipping\Application\UseCase\GetOrder\GetOrderUseCase;
use Module\User\Domain\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class OrderGetController {
	public function __construct(private GetOrderUseCase $getOrderUseCase) {}

	public function __invoke(string $orderId, ServerRequestInterface $request, ResponseInterface $response) {
		/** @var User */
		$user = $request->getAttribute('user');

		$order = $this->getOrderUseCase->execute($orderId, $user->id);
		if ($order === null) {
			$response->getBody()->write(json_encode(['error' => 'Order not found']));
			return $response->withStatus(404);
		}

		$response->getBody()->write(json_encode(['order' => $order]));
		return $response;
	}
}
