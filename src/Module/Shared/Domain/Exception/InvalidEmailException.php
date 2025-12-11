<?php

namespace Module\Shared\Domain\Exception;

final class InvalidEmailException extends \DomainException {
	public function __construct(string $email) {
		return parent::__construct("Invalid email address: $email");
	}
}
