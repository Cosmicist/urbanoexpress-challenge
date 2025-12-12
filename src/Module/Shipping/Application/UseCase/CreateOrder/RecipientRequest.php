<?php

namespace Module\Shipping\Application\UseCase\CreateOrder;

final class RecipientRequest {
	public function __construct(
		public readonly string $name,
		public readonly string $address1,
		public readonly string $address2,
		public readonly string $city,
		public readonly string $state,
		public readonly string $postalCode,
		public readonly ?string $phone = null,
		public readonly ?string $email = null
	) {}
}
