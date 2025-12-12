<?php

namespace Module\Shipping\Application\UseCase\CreateOrder;

final class OrderItemRequest {
	public function __construct(
		public readonly string $sku,
		public readonly string $name,
		public readonly int $quantity,
		public readonly float $unitPrice,
		public readonly float $unitWeight,
	) {}
}
