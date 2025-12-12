<?php

namespace Module\Shipping\Domain;

use Doctrine\Persistence\ObjectRepository;

interface OrderRepository extends ObjectRepository {
	public function findOne(string $id, string $customerId): ?Order;

	/** @return Order[] */
	public function findAllForCustomer(string $customerId): array;
}
