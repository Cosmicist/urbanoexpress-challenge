<?php

namespace Module\Shipping\Domain\Exception;

final class InvalidOrderItemWeightException extends \InvalidArgumentException {
	public function __construct(float $weight) {
		parent::__construct("Invalid order item weight: {$weight}. Weight cannot be negative.");
	}
}
