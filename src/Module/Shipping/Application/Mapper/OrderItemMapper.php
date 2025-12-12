<?php

namespace Module\Shipping\Application\Mapper;

use DateTimeInterface;
use Module\Shipping\Domain\OrderItem;

final class OrderItemMapper {
	public static function toArray(OrderItem $orderItem): array {
		return [
			'id' => $orderItem->id,
			'sku' => $orderItem->sku,
			'name' => $orderItem->name,
			'quantity' => $orderItem->quantity,
			'unit_price' => $orderItem->unitPrice,
			'total_price' => $orderItem->totalPrice,
			'unit_weight' => $orderItem->unitWeight,
			'total_weight' => $orderItem->totalWeight,
			'created_at' => $orderItem->createdAt->format(DateTimeInterface::ATOM),
			'updated_at' => $orderItem->updatedAt->format(DateTimeInterface::ATOM),
		];
	}
}
