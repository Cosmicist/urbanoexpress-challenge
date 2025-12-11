<?php

namespace Module\Shipping\Domain\Exception;

final class OrderMustHaveItemsException extends \DomainException {
	public function __construct() {
		parent::__construct('An order must have at least one item.');
	}
}
