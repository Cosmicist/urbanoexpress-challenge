<?php

namespace Module\Shared\Domain;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;
use JsonSerializable;
use Module\Shared\Domain\Exception\InvalidEmailException;

#[Embeddable]
final class Email implements JsonSerializable {
	#[Column(type: 'string', name: 'email', nullable: true)]
	private(set) ?string $value;

	private function __construct(string $value) {
		if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
			throw new InvalidEmailException($value);
		}
		$this->value = $value;
	}

	public static function fromString(string $value): self {
		return new self($value);
	}

	public function equals(Email $other): bool {
		return $this->value === $other->value;
	}

	public function jsonSerialize(): string {
		return $this->value;
	}

	public function __toString(): string {
		return $this->value;
	}
}
