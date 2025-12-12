<?php

namespace Module\Shipping\Infra\Db;

use Doctrine\ORM\EntityRepository;
use Module\Shipping\Domain\Order;
use Module\Shipping\Domain\OrderRepository;

/**
 * @extends EntityRepository<Order>
 */
final class DoctrineOrderRepository extends EntityRepository implements OrderRepository {
	public function findOne(string $id, string $customerId): ?Order {
		return $this->createQueryBuilder('o')
			->leftJoin('o.items', 'i')
			->addSelect('i')
			->where('o.id = :id')
			->setParameter('id', $id)
			->andWhere('o.customerId = :customerId')
			->setParameter('customerId', $customerId)
			->setMaxResults(1)
			->getQuery()
			->getOneOrNullResult();
	}

	public function findByExternalOrderId(string $externalOrderId, string $customerId): ?Order {
		return $this->createQueryBuilder('o')
			->leftJoin('o.items', 'i')
			->addSelect('i')
			->where('o.externalOrderId = :externalOrderId')
			->setParameter('externalOrderId', $externalOrderId)
			->andWhere('o.customerId = :customerId')
			->setParameter('customerId', $customerId)
			->setMaxResults(1)
			->getQuery()
			->getOneOrNullResult();
	}

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
