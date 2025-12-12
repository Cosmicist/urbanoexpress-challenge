<?php

namespace Module\Shipping\Application\Mapper;

use Module\Shipping\Domain\Recipient;

final class RecipientMapper {
		public static function toArray(Recipient $recipient): array {
				return [
						'name' => $recipient->name,
						'phone' => $recipient->phoneNumber,
						'email' => $recipient->email->value ?? null,
						'address_1' => $recipient->addressLine1,
						'address_2' => $recipient->addressLine2,
						'city' => $recipient->city,
						'postalCode' => $recipient->postalCode,
						'full_address' => $recipient->fullAddress,
				];
		}
}
