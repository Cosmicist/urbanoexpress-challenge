<?php

namespace Module\Shipping\Domain\Exception;

final class InvalidOrderItemPriceException extends \InvalidArgumentException {
	public function __construct(float $price) {
		parent::__construct("Invalid order item price: {$price}. Price cannot be negative.");
	}
}
