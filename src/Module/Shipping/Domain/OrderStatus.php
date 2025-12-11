<?php

namespace Module\Shipping\Domain;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
final class OrderStatus {
	public const CREATED = 'created';
	public const IN_TRANSIT = 'in_transit';
	public const OUT_FOR_DELIVERY = 'out_for_delivery';
	public const DELIVERED = 'delivered';
	public const CANCELLED = 'cancelled';

	#[Column(type: 'string', name: 'status')]
	private $value;

	private function __construct(string $value) {
		$this->value = $value;
	}

	// --- Factory Methods --- //

	public static function Created(): self {
		return new self(self::CREATED);
	}

	public static function InTransit(): self {
		return new self(self::IN_TRANSIT);
	}

	public static function OutForDelivery(): self {
		return new self(self::OUT_FOR_DELIVERY);
	}

	public static function Delivered(): self {
		return new self(self::DELIVERED);
	}

	public static function Cancelled(): self {
		return new self(self::CANCELLED);
	}

	// --- Checks --- //

	public function isCreated(): bool {
		return $this->value === self::CREATED;
	}

	public function isInTransit(): bool {
		return $this->value === self::IN_TRANSIT;
	}

	public function isOutForDelivery(): bool {
		return $this->value === self::OUT_FOR_DELIVERY;
	}

	public function isDelivered(): bool {
		return $this->value === self::DELIVERED;
	}

	public function isCancelled(): bool {
		return $this->value === self::CANCELLED;
	}

	public function isFinal(): bool {
		return $this->isDelivered() || $this->isCancelled();
	}

	// --- Other Methods --- //

	public function getValue(): string {
		return $this->value;
	}

	public function equals(OrderStatus $status): bool {
		return $this->value === $status->getValue();
	}

	public function __toString() {
		return $this->value;
	}
}
