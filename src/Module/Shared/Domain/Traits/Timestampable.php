<?php

namespace Module\Shared\Domain\Traits;

use Doctrine\ORM\Mapping as ORM;

trait Timestampable {
	#[ORM\Column(name: 'created_at', type: 'datetimetz_immutable', nullable: true, options: ['default' => 'CURRENT_TIMESTAMP'])]
	protected(set) \DateTimeInterface $createdAt;

	#[ORM\Column(name: 'updated_at', type: 'datetimetz_immutable', nullable: true, options: ['default' => 'CURRENT_TIMESTAMP'])]
	protected(set) \DateTimeInterface $updatedAt;

	#[ORM\PreUpdate]
	public function onPreUpdate(): void {
		$this->updatedAt = new \DateTimeImmutable();
	}

	#[ORM\PrePersist]
	public function onPrePersist(): void {
		$this->createdAt = new \DateTimeImmutable();
		$this->updatedAt = new \DateTimeImmutable();
	}
}
