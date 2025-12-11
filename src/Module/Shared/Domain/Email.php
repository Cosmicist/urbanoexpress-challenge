<?php

namespace Module\Shared\Domain;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;
use Module\Shared\Domain\Exception\InvalidEmailException;

#[Embeddable]
final class Email {
	#[Column(type: 'string', name: 'email')]
	private string $value;

	private function __construct(string $value) {
		if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
			throw new InvalidEmailException($value);
		}
		$this->value = $value;
	}

	public static function fromString(string $value): self {
		return new self($value);
	}

	public function __toString(): string {
		return $this->value;
	}

	public function equals(Email $other): bool {
		return $this->value === $other->value;
	}
}
