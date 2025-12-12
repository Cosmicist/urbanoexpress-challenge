<?php

use Doctrine\ORM\EntityManager;
use Module\Shared\Domain\Exception\InvalidEmailException;
use Module\Shipping\Application\UseCase\CreateOrder\CreateOrderUseCase;
use Module\Shipping\Application\UseCase\CreateOrder\OrderItemRequest;
use Module\Shipping\Application\UseCase\CreateOrder\RecipientRequest;
use Module\Shipping\Domain\Exception\InvalidOrderItemPriceException;
use Module\Shipping\Domain\Exception\InvalidOrderItemQuantityException;
use Module\Shipping\Domain\Exception\InvalidOrderItemWeightException;
use Module\Shipping\Domain\Exception\OrderMustHaveItemsException;
use Module\Shipping\Domain\Order;

describe('CreateOrderUseCase', function () {
	it('creates an order and returns its ID', function () {
		$entityManager = Mockery::mock(EntityManager::class);

		$entityManager
		->shouldReceive('persist')
		->with(Mockery::on(function ($order) {
			if (!($order instanceof Order)) {
				return false;
			}

			/** @var Order $order */
			new ReflectionClass($order)
			->getProperty('id')
			->setValue($order, 'generated-order-id');

			return true;
		}));

		$entityManager
		->shouldReceive('flush');

		$useCase = new CreateOrderUseCase($entityManager);

		$externalOrderId = 'external-123';
		$customerId = 'customer-456';
		$recipientRequest = new RecipientRequest(
			'John Doe',
			'123 Main St',
			'Anytown',
			'City',
			'State',
			'12345',
			'555-1234',
			'john.doe@example.com',
		);
		$orderItems = [
			new OrderItemRequest(
				'sku-001',
				'Product 1',
				2,
				19.99,
				1.5
			),
			new OrderItemRequest(
				'sku-002',
				'Product 2',
				1,
				9.99,
				0.5
			),
		];
		$notes = 'Please deliver between 9am-5pm';

		$orderId = $useCase->execute(
			$externalOrderId,
			$customerId,
			$recipientRequest,
			$orderItems,
			$notes
		);

		expect($orderId)->toBe('generated-order-id');
	});

	it('throws an exception when no items are provided', function () {
		$entityManager = Mockery::mock(EntityManager::class);
		$useCase = new CreateOrderUseCase($entityManager);

		$externalOrderId = 'external-123';
		$customerId = 'customer-456';
		$recipientRequest = new RecipientRequest(
			'John Doe',
			'123 Main St',
			'Anytown',
			'City',
			'State',
			'12345',
			'555-1234',
			'john.doe@example.com',
		);
		$orderItems = [];

		$this->expectException(OrderMustHaveItemsException::class);

		$useCase->execute(
			$externalOrderId,
			$customerId,
			$recipientRequest,
			$orderItems,
		);
	});

	it('throws an exception when an invalid recipient email is provided', function () {
		$entityManager = Mockery::mock(EntityManager::class);
		$useCase = new CreateOrderUseCase($entityManager);

		$externalOrderId = 'external-123';
		$customerId = 'customer-456';
		$recipientRequest = new RecipientRequest(
			'John Doe',
			'123 Main St',
			'Anytown',
			'City',
			'State',
			'12345',
			'555-1234',
			'invalid-email',
		);
		$orderItems = [
			new OrderItemRequest(
				'sku-001',
				'Product 1',
				2,
				19.99,
				1.5
			),
		];

		$this->expectException(InvalidEmailException::class);

		$useCase->execute(
			$externalOrderId,
			$customerId,
			$recipientRequest,
			$orderItems,
		);
	});

	it('throws an exception when an invalid item request is provided', function () {
		$entityManager = Mockery::mock(EntityManager::class);
		$useCase = new CreateOrderUseCase($entityManager);

		$externalOrderId = 'external-123';
		$customerId = 'customer-456';
		$recipientRequest = new RecipientRequest(
			'John Doe',
			'123 Main St',
			'Anytown',
			'City',
			'State',
			'12345',
			'555-1234',
			'john.doe@example.com',
		);
		$orderItems = [ new stdClass() ]; // Invalid item request

		$this->expectException(InvalidArgumentException::class);

		$useCase->execute(
			$externalOrderId,
			$customerId,
			$recipientRequest,
			$orderItems,
		);
	});

	it('throws an exception when an item has invalid quantity, price, or weight', function ($orderItems, $expectedException) {
		$entityManager = Mockery::mock(EntityManager::class);
		$useCase = new CreateOrderUseCase($entityManager);

		$externalOrderId = 'external-123';
		$customerId = 'customer-456';
		$recipientRequest = new RecipientRequest(
			'John Doe',
			'123 Main St',
			'Anytown',
			'City',
			'State',
			'12345',
			'555-1234',
			'john.doe@example.com',
		);

		$this->expectException($expectedException);

		$useCase->execute(
			$externalOrderId,
			$customerId,
			$recipientRequest,
			$orderItems,
		);
	})->with([
		[
				[new OrderItemRequest('sku-001', 'Product 1', 0, 19.99, 1.5)], // Invalid quantity
				InvalidOrderItemQuantityException::class
		],
		[
			[new OrderItemRequest('sku-001', 'Product 1', 2, -5.00, 1.5)], // Invalid price
			InvalidOrderItemPriceException::class
		],
		[
			[new OrderItemRequest('sku-001', 'Product 1', 2, 19.99, -1.0)], // Invalid weight
			InvalidOrderItemWeightException::class
		],
	]);
});
