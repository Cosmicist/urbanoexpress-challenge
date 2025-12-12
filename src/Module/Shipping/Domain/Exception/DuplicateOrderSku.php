<?php

namespace Module\Shipping\Domain\Exception;

final class DuplicateOrderSku extends \Exception {
	public function __construct(string $sku) {
		parent::__construct("Duplicate SKU found in order items: '$sku'.");
	}
}
