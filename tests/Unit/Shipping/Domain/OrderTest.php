<?php

use Doctrine\Common\Collections\ArrayCollection;
use Module\Shipping\Domain\Exception\CannotCancelWithoutReasonException;
use Module\Shipping\Domain\Exception\InvalidTransitionException;
use Module\Shipping\Domain\Exception\OrderMustHaveItemsException;
use Module\Shipping\Domain\Order;
use Module\Shipping\Domain\OrderItem;
use Module\Shipping\Domain\Recipient;

describe('Shipping Order', function () {
	describe('Initialization', function () {
		it('should throw an exception when created without items', function () {
			$closure = function () {
				$recipient = new Recipient('John Doe', '123 Main St', 'Cityville', 'Stateville', '12345');
				new Order('EXT123', 'CUST123', $recipient, new ArrayCollection());
			};

			expect($closure)->toThrow(OrderMustHaveItemsException::class);
		});
	});

	describe('Computed Properties', function () {
		it('should calculate total price and weight correctly', function () {
			$item1 = new OrderItem('SKU123', 'Item 1', 2, 10.00, 1.5);
			$item2 = new OrderItem('SKU456', 'Item 2', 1, 20.00, 2.0);
			$items = new ArrayCollection([$item1, $item2]);

			$recipient = new Recipient('John Doe', '123 Main St', 'Cityville', 'Stateville', '12345');
			$order = new Order('EXT123', 'CUST123', $recipient, $items);

			$expectedTotalPrice = ($item1->totalPrice + $item2->totalPrice);
			$expectedTotalWeight = ($item1->totalWeight + $item2->totalWeight);

			expect($order->totalPrice)->toBe($expectedTotalPrice);
			expect($order->totalWeight)->toBe($expectedTotalWeight);
		});
	});

	describe('Status Transitions', function () {
		it('should allow valid status transitions', function () {
			$recipient = new Recipient('John Doe', '123 Main St', 'Cityville', 'Stateville', '12345');
			$items = new ArrayCollection([ new OrderItem('SKU123', 'Item 1', 2, 10.00, 1.5) ]);
			$order = new Order('EXT123', 'CUST123', $recipient, $items);

			$order->setInTransit();
			expect($order->status->isInTransit())->toBeTrue();

			$order->setOutForDelivery();
			expect($order->status->isOutForDelivery())->toBeTrue();

			$order->setDelivered();
			expect($order->status->isDelivered())->toBeTrue();
		});

		it('should allow transition to "Out For Delivery" directly from "Created"', function () {
			$recipient = new Recipient('John Doe', '123 Main St', 'Cityville', 'Stateville', '12345');
			$items = new ArrayCollection([ new OrderItem('SKU123', 'Item 1', 2, 10.00, 1.5) ]);
			$order = new Order('EXT123', 'CUST123', $recipient, $items);
			$order->setOutForDelivery();

			expect($order->status->isOutForDelivery())->toBeTrue();
		});

		it('should only allow cancellation from "Created"', function () {
			$recipient = new Recipient('John Doe', '123 Main St', 'Cityville', 'Stateville', '12345');
			$items = new ArrayCollection([ new OrderItem('SKU123', 'Item 1', 2, 10.00, 1.5) ]);

			$orderA = new Order('EXT123', 'CUST123', $recipient, $items);
			$orderA->setCancelled('Customer requested cancellation');

			expect($orderA->status->isCancelled())->toBeTrue();
			expect($orderA->cancelReason)->toBe('Customer requested cancellation');

			$orderB = new Order('EXT124', 'CUST124', $recipient, $items);
			$orderB->setInTransit();

			expect($orderA->status->isCancelled())->toBeTrue();
			expect(fn () => $orderB->setCancelled('Customer requested cancellation'))->toThrow(InvalidTransitionException::class);

			$orderB->setOutForDelivery();
			expect(fn () => $orderB->setCancelled('Customer requested cancellation'))->toThrow(InvalidTransitionException::class);

			$orderB->setDelivered();
			expect(fn () => $orderB->setCancelled('Customer requested cancellation'))->toThrow(InvalidTransitionException::class);
		});

		it('should prevent cancellation without a reason', function () {
			$recipient = new Recipient('John Doe', '123 Main St', 'Cityville', 'Stateville', '12345');
			$items = new ArrayCollection([ new OrderItem('SKU123', 'Item 1', 2, 10.00, 1.5) ]);
			$order = new Order('EXT123', 'CUST123', $recipient, $items);

			expect(fn () => $order->setCancelled(''))->toThrow(CannotCancelWithoutReasonException::class);
		});

		it('should prevent other invalid status transitions', function () {
			$recipient = new Recipient('John Doe', '123 Main St', 'Cityville', 'Stateville', '12345');
			$items = new ArrayCollection([ new OrderItem('SKU123', 'Item 1', 2, 10.00, 1.5) ]);
			$order = new Order('EXT123', 'CUST123', $recipient, $items);

			expect(fn () => $order->setDelivered())->toThrow(InvalidTransitionException::class);

			$order->setInTransit();
			expect(fn () => $order->setDelivered())->toThrow(InvalidTransitionException::class);

			$order->setOutForDelivery();
			expect(fn () => $order->setInTransit())->toThrow(InvalidTransitionException::class);

			$order->setDelivered();
			expect(fn () => $order->setInTransit())->toThrow(InvalidTransitionException::class);
		});
	});
});
