<?php

namespace Module\Shipping\Domain\Exception;

final class InvalidOrderItemQuantityException extends \InvalidArgumentException {
	public function __construct(int $quantity) {
		parent::__construct("Invalid order item quantity: {$quantity}. Quantity must be at least 1.");
	}
}
