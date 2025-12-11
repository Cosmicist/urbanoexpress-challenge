<?php

namespace Module\Shipping\Domain\Exception;

final class CannotCancelWithoutReasonException extends \DomainException {
	public function __construct(?string $orderId = null) {
		parent::__construct('Cannot cancel order ' . ("$orderId " ?? '') . 'without providing a reason.');
	}
}
