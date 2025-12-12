<?php

namespace Module\User\Domain;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;
use Module\Shared\Domain\Email;
use Module\Shared\Domain\Traits\Timestampable;
use Ramsey\Uuid\Doctrine\UuidV7Generator;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'users')]
final class User
{
	use Timestampable;

	#[ORM\Id]
	#[ORM\Column(type: 'uuid', unique: true)]
	#[ORM\GeneratedValue(strategy: 'CUSTOM')]
	#[ORM\CustomIdGenerator(class: UuidV7Generator::class)]
	private(set) ?string $id = null;

	#[ORM\Column(type: 'string')]
	private(set) string $name;

	#[Embedded(Email::class, columnPrefix: false)]
	private(set) Email $email;

	#[ORM\Column(name: 'access_token', type: 'string')]
	public string $accessToken;

	public function __construct(string $name, Email $email) {
		$this->name = $name;
		$this->email = $email;
	}
}
