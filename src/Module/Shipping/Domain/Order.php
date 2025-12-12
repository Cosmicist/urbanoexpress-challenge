<?php

namespace Module\Shipping\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Module\Shipping\Domain\Exception\InvalidTransitionException;
use Module\Shared\Domain\Traits\Timestampable;
use Module\Shipping\Domain\Exception\CannotCancelWithoutReasonException;
use Module\Shipping\Domain\Exception\OrderMustHaveItemsException;
use Ramsey\Uuid\Doctrine\UuidV7Generator;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'orders')]
final class Order {
	use Timestampable;

	#[ORM\Id]
	#[ORM\Column(type: 'uuid', unique: true)]
	#[ORM\GeneratedValue(strategy: 'CUSTOM')]
	#[ORM\CustomIdGenerator(class: UuidV7Generator::class)]
	private(set) ?string $id;

	#[ORM\Column(name: 'external_order_id', type: 'string', unique: true)]
	private(set) string $externalOrderId;

	#[ORM\Column(name: 'customer_id', type: 'string')]
	private(set) string $customerId;

	#[ORM\Embedded(class: OrderStatus::class, columnPrefix: false)]
	private(set) OrderStatus $status;

	#[ORM\Embedded(class: Recipient::class)]
	private(set) Recipient $recipient;

	/** @var Collection<int,OrderItem> */
	#[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'order', cascade: ['persist', 'remove'], orphanRemoval: true)]
	private(set) Collection $items;

	#[ORM\Column(nullable: true)]
	public ?string $notes;

	private(set) ?string $cancelReason {
		set {
			if (!$this->status->isCancelled() || $value === '' || $value === null) {
				throw new CannotCancelWithoutReasonException($this->id);
			}
			$this->cancelReason = $value;
		}
	}

	public function __construct(string $externalOrderId, string $customerId, Recipient $recipient, Collection $items = new ArrayCollection(), ?string $notes = null)
	{
		if ($items->isEmpty()) {
			throw new OrderMustHaveItemsException();
		}

		$this->id = null;
		$this->externalOrderId = $externalOrderId;
		$this->customerId = $customerId;
		$this->status = OrderStatus::Created();
		$this->recipient = $recipient;
		$this->items = $items;
		$this->notes = $notes;
	}

	// --- Computed Properties --- //

	public float $totalPrice {
		get {
			return $this->items->reduce(fn ($total, OrderItem $item) => $total += $item->totalPrice, 0.0);
		}
	}

	public float $totalWeight {
		get {
			return $this->items->reduce(fn ($total, OrderItem $item) => $total += $item->totalWeight, 0.0);
		}
	}

	// --- State Transitions --- //

	/**
	 * @throws InvalidTransitionException
	 */
	public function setInTransit(): void {
		$this->transitionTo(OrderStatus::InTransit());
	}

	/**
	 * @throws InvalidTransitionException
	 */
	public function setOutForDelivery(): void {
		$this->transitionTo(OrderStatus::OutForDelivery());
	}

	/**
	 * @throws InvalidTransitionException
	 */
	public function setDelivered(): void {
		$this->transitionTo(OrderStatus::Delivered());
	}

	/**
	 * @throws InvalidTransitionException
	 */
	public function setCancelled(string $reason): void {
		$this->transitionTo(OrderStatus::Cancelled());
		$this->cancelReason = $reason;
	}

	/**
	 * @throws InvalidTransitionException
	 */
	private function transitionTo(OrderStatus $newStatus): void {
		if (!$this->canTransitionTo($newStatus)) {
			throw new InvalidTransitionException($this->status->value, $newStatus->value);
		}

		$this->status = $newStatus;
	}

	private function canTransitionTo(OrderStatus $newStatus): bool {
		$current = $this->status;

		if ($current->isFinal()) {
			return false;
		}

		// Can transition to "out_for_delivery" from "created" when wharehousing
		if ($current->isCreated()) {
			return $newStatus->isInTransit() || $newStatus->isOutForDelivery() || $newStatus->isCancelled();
		}

		if ($current->isInTransit()) {
			return $newStatus->isOutForDelivery();
		}

		if ($current->isOutForDelivery()) {
			return $newStatus->isDelivered();
		}

		return false;
	}
}
