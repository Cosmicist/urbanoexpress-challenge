<?php

namespace Module\Shipping\Infra\Http;

use Module\Shipping\Application\UseCase\CreateOrder\CreateOrderUseCase;
use Module\Shipping\Application\UseCase\CreateOrder\OrderItemRequest;
use Module\Shipping\Application\UseCase\CreateOrder\RecipientRequest;
use Module\User\Domain\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class OrderCreateController {
	public function __construct(private CreateOrderUseCase $createOrderUseCase) {}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response) {
		/** @var User */
		$user = $request->getAttribute('user');
		$data = $request->getParsedBody();

		$recipient = new RecipientRequest(
			$data['recipient']['name'],
			$data['recipient']['address_1'],
			$data['recipient']['address_2'] ?? null,
			$data['recipient']['city'],
			$data['recipient']['state'],
			$data['recipient']['postal_code'],
			$data['recipient']['phone_number'] ?? null,
			$data['recipient']['email'] ?? null
		);

		$orderItems = [];
		foreach ($data['items'] as $itemData) {
			$orderItems[] = new OrderItemRequest(
				$itemData['sku'],
				$itemData['name'],
				$itemData['quantity'],
				$itemData['unit_price'],
				$itemData['unit_weight'],
			);
		}

		try {
			$orderId = $this->createOrderUseCase->execute(
				externalOrderId: $data['external_order_id'],
				customerId: $user->id, // TODO: Get customer ID from auth context
				recipient: $recipient,
				items: $orderItems,
				notes: $data['notes'] ?? null,
			);
		} catch (\Exception $e) {
			$response->getBody()->write(json_encode(['error' => $e->getMessage()]));
			return $response->withStatus(400);
		}

		$response->getBody()->write(json_encode(['message' => 'Order created', 'order_id' => $orderId]));
		return $response->withStatus(201);
	}
}
