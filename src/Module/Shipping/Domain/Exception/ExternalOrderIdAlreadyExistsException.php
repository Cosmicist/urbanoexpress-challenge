<?php

namespace Module\Shipping\Domain\Exception;

final class ExternalOrderIdAlreadyExistsException extends \DomainException {
	public function __construct(string $externalOrderId) {
		parent::__construct("Order with external ID '{$externalOrderId}' already exists.");
	}
}
