<?php

namespace Module\Shipping\Domain;

use Doctrine\ORM\Mapping as ORM;
use Module\Shared\Domain\Email;

#[ORM\Embeddable]
final class Recipient {
	#[ORM\Column(type: 'string')]
	public string $name;

	#[ORM\Column(type: 'string', name: 'address_line_1')]
	public string $addressLine1;

	#[ORM\Column(type: 'string', name: 'address_line_2', nullable: true)]
	public ?string $addressLine2;

	#[ORM\Column(type: 'string')]
	public string $city;

	#[ORM\Column(type: 'string')]
	public string $state;

	#[ORM\Column(type: 'string', name: 'postal_code')]
	public string $postalCode;

	#[ORM\Embedded(class: Email::class, columnPrefix: false)]
	public ?Email $email;

	#[ORM\Column(type: 'string', name: 'phone_number', nullable: true)]
	public ?string $phoneNumber;

	public function __construct(string $name, string $addressLine1, string $city, string $state, string $postalCode, ?Email $email = null, ?string $phoneNumber = null, ?string $addressLine2 = null) {
		$this->name = $name;
		$this->addressLine1 = $addressLine1;
		$this->addressLine2 = $addressLine2;
		$this->city = $city;
		$this->state = $state;
		$this->postalCode = $postalCode;
		$this->email = $email;
		$this->phoneNumber = $phoneNumber;
	}

	public string $fullAddress {
		get {
			$address = $this->addressLine1;
			if ($this->addressLine2) {
				$address .= ', ' . $this->addressLine2;
			}
			$address .= ', ' . $this->city . ', ' . $this->state . ' ' . $this->postalCode;

			return $address;
		}
	}
}
