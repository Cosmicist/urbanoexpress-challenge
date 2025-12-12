<?php

namespace Module\Shipping\Infra\Db;

use Doctrine\ORM\EntityRepository;
use Module\Shipping\Domain\Order;
use Module\Shipping\Domain\OrderRepository;

/**
 * @extends EntityRepository<Order>
 */
final class DoctrineOrderRepository extends EntityRepository implements OrderRepository {
	/** @return Order[] */
	public function findAllForCustomer(string $customerId): array {
		return $this->createQueryBuilder('o')
			->leftJoin('o.items', 'i')
			->addSelect('i')
			->where('o.customerId = :customerId')
			->setParameter('customerId', $customerId)
			->orderBy('o.id', 'DESC')
			->getQuery()
			->getResult();
	}
}
