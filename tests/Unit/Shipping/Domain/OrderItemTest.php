<?php

use Module\Shipping\Domain\Exception\InvalidOrderItemPriceException;
use Module\Shipping\Domain\Exception\InvalidOrderItemQuantityException;
use Module\Shipping\Domain\Exception\InvalidOrderItemWeightException;
use Module\Shipping\Domain\OrderItem;

describe('Shipping OrderItem', function() {
		it('should calculate total price and weight correctly', function() {
			$orderItem = new OrderItem('SKU123', 'Test Item', 3, 15.00, 2.0);

			$expectedTotalPrice = 3 * 15.00;
			$expectedTotalWeight = 3 * 2.0;

			expect($orderItem->totalPrice)->toBe($expectedTotalPrice);
			expect($orderItem->totalWeight)->toBe($expectedTotalWeight);
		});

		it('should throw an exception for invalid quantity', function() {
			$closure = function() {
				new OrderItem('SKU123', 'Test Item', 0, 15.00, 2.0);
			};

			expect($closure)->toThrow(InvalidOrderItemQuantityException::class);
		});

		it('should throw an exception for negative unit price', function() {
			$closure = function() {
				new OrderItem('SKU123', 'Test Item', 2, -5.00, 2.0);
			};

			expect($closure)->toThrow(InvalidOrderItemPriceException::class);
		});

		it('should throw an exception for negative unit weight', function() {
			$closure = function() {
				new OrderItem('SKU123', 'Test Item', 2, 15.00, -1.0);
			};

			expect($closure)->toThrow(InvalidOrderItemWeightException::class);
		});
});
