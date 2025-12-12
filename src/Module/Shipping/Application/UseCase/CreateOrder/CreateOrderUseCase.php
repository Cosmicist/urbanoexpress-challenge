<?php

namespace Module\Shipping\Application\UseCase\CreateOrder;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Module\Shared\Domain\Email;
use Module\Shared\Domain\Exception\InvalidEmailException;
use Module\Shipping\Domain\Exception\InvalidOrderItemPriceException;
use Module\Shipping\Domain\Exception\InvalidOrderItemQuantityException;
use Module\Shipping\Domain\Exception\InvalidOrderItemWeightException;
use Module\Shipping\Domain\Exception\ExternalOrderIdAlreadyExistsException;
use Module\Shipping\Domain\Exception\OrderMustHaveItemsException;
use Module\Shipping\Domain\Order;
use Module\Shipping\Domain\OrderItem;
use Module\Shipping\Domain\OrderRepository;
use Module\Shipping\Domain\Recipient;

final class CreateOrderUseCase {
	public function __construct(private EntityManager $em, private OrderRepository $orderRepository) {}

	/**
	 * @param OrderItemRequest[] $items
	 * @return string The ID of the created order
	 *
	 * @throws OrderMustHaveItemsException
	 * @throws InvalidOrderItemQuantityException
	 * @throws InvalidOrderItemPriceException
	 * @throws InvalidOrderItemWeightException
	 * @throws InvalidEmailException
	 * @throws \InvalidArgumentException
	 */
	public function execute(
		string $externalOrderId,
		string $customerId,
		RecipientRequest $recipient,
		array $items,
		?string $notes = null
	): string {

		if ($this->orderRepository->findByExternalOrderId($externalOrderId, $customerId)) {
			throw new ExternalOrderIdAlreadyExistsException($externalOrderId);
		}

		$email = $recipient->email ? Email::fromString($recipient->email) : null;

		$recipient = new Recipient(
			$recipient->name,
			$recipient->address1,
			$recipient->city,
			$recipient->state,
			$recipient->postalCode,
			$email,
			$recipient->phone,
			$recipient->address2,
		);

		$orderItems = new ArrayCollection();
		foreach ($items as $itemRequest) {
			if (!($itemRequest instanceof OrderItemRequest)) {
				throw new \InvalidArgumentException('Invalid item request provided');
			}

			$orderItems->add(
				new OrderItem(
					$itemRequest->sku,
					$itemRequest->name,
					$itemRequest->quantity,
					$itemRequest->unitPrice,
					$itemRequest->unitWeight,
				)
			);
		}

		$order = new Order(
			$externalOrderId,
			$customerId,
			$recipient,
			$orderItems,
			$notes,
		);

		$this->em->persist($order);
		$this->em->flush();

		return $order->id;
	}
}
