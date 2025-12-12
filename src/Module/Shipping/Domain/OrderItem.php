<?php

namespace Module\Shipping\Domain;

use Doctrine\ORM\Mapping as ORM;
use Module\Shared\Domain\Traits\Timestampable;
use Module\Shipping\Domain\Exception\InvalidOrderItemPriceException;
use Module\Shipping\Domain\Exception\InvalidOrderItemQuantityException;
use Module\Shipping\Domain\Exception\InvalidOrderItemWeightException;
use Ramsey\Uuid\Doctrine\UuidV7Generator;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'order_items')]
final class OrderItem {
	use Timestampable;

	#[ORM\Id]
	#[ORM\Column(type: 'uuid', unique: true)]
	#[ORM\GeneratedValue(strategy: 'CUSTOM')]
	#[ORM\CustomIdGenerator(class: UuidV7Generator::class)]
	private(set) ?string $id = null;

	#[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
	private(set) Order $order;

	/** External product SKU of the item */
	#[ORM\Column(type: 'string', name: 'sku')]
	public string $sku;

	/** Name/Description of the item */
	#[ORM\Column(type: 'string', name: 'name')]
	public string $name;

	#[ORM\Column(type: 'integer')]
	public int $quantity {
		set {
			if ($value < 1) {
				throw new InvalidOrderItemQuantityException($value);
			}
			$this->quantity = $value;
		}
	}

	#[ORM\Column(type: 'decimal', precision: 10, scale: 2, name: 'unit_price')]
	public float $unitPrice {
		set {
			if ($value < 0) {
				throw new InvalidOrderItemPriceException($value);
			}
			$this->unitPrice = $value;
		}
	}

	#[ORM\Column(type: 'decimal', precision: 10, scale: 2, name: 'unit_weight')]
	public float $unitWeight {
		set {
			if ($value < 0) {
				throw new InvalidOrderItemWeightException($value);
			}
			$this->unitWeight = $value;
		}
	}

	public function __construct(string $sku, string $name, int $quantity, float $unitPrice, float $unitWeight) {
		$this->id = null;
		$this->sku = $sku;
		$this->name = $name;
		$this->quantity = $quantity;
		$this->unitPrice = $unitPrice;
		$this->unitWeight = $unitWeight;
	}

	public float $totalPrice {
		get {
			return $this->quantity * $this->unitPrice;
		}
	}

	public float $totalWeight {
		get {
			return $this->quantity * $this->unitWeight;
		}
	}
}
