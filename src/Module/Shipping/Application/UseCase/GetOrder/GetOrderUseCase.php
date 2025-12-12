<?php

namespace Module\Shipping\Application\UseCase\GetOrder;

use Module\Shipping\Application\Mapper\OrderMapper;
use Module\Shipping\Domain\OrderRepository;

final class GetOrderUseCase {
		public function __construct(private OrderRepository $orderRepository) {}

		public function execute(string $orderId, string $customerId): ?array {
				$order = $this->orderRepository->findOne($orderId, $customerId);
				if ($order === null) {
						return null;
				}

				return OrderMapper::toArray($order);
		}
}
