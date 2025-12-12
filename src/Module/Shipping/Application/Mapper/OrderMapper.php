<?php

namespace Module\Shipping\Application\Mapper;

use DateTimeInterface;
use Module\Shipping\Domain\Order;

final class OrderMapper {
	public static function toArray(Order $order): array {
		return [
			'id' => $order->id,
			'customerId' => $order->customerId,
			'externalOrderId' => $order->externalOrderId,
			'status' => $order->status->value,
			'notes' => $order->notes,
			'totalPrice' => $order->totalPrice,
			'totalWeight' => $order->totalWeight,
			'recipient' => RecipientMapper::toArray($order->recipient),
			'items' => $order->items->map(fn ($item) => OrderItemMapper::toArray($item))->toArray(),
			'created_at' => $order->createdAt->format(DateTimeInterface::ATOM),
			'updated_at' => $order->updatedAt->format(DateTimeInterface::ATOM),
		];
	}

	public static function toArrayList(array $orders): array {
		return array_map(
			fn ($order) => self::toArray($order),
			$orders
		);
	}
}
