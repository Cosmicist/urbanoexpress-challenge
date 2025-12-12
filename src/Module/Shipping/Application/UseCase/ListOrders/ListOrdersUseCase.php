<?php

namespace Module\Shipping\Application\UseCase\ListOrders;

use Module\Shipping\Application\Mapper\OrderMapper;
use Module\Shipping\Domain\OrderRepository;

final class ListOrdersUseCase {
	public function __construct(private OrderRepository $orderRepository) {}

	public function execute(string $customerId): array {
		$orders = $this->orderRepository->findAllForCustomer($customerId);
		return OrderMapper::toArrayList($orders);
	}
}
