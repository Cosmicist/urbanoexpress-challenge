<?php

namespace Module\Shipping\Domain;

use Doctrine\Persistence\ObjectRepository;

interface OrderRepository extends ObjectRepository {
	/** @return Order[] */
	public function findAllForCustomer(string $customerId): array;
}
