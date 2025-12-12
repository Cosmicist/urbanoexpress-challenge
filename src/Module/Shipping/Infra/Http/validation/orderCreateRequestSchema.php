<?php

return <<<'JSON'
{
	"type": "object",
	"properties": {
		"external_order_id": { "type": "string", "minLength": 1 },
		"recipient": {
			"type": "object",
			"properties": {
				"name": { "type": "string", "minLength": 1 },
				"address_1": { "type": "string", "minLength": 1 },
				"address_2": { "type": "string" },
				"city": { "type": "string", "minLength": 1 },
				"state": { "type": "string", "minLength": 1 },
				"postal_code": { "type": "string", "minLength": 1 },
				"email": { "type": "string", "format": "email" },
				"phone_number": { "type": "string" }
			},
			"required": ["name", "address_1", "city", "state", "postal_code"]
		},
		"items": {
			"type": "array",
			"minItems": 1,
			"items": {
				"type": "object",
				"properties": {
					"sku": { "type": "string", "minLength": 1 },
					"name": { "type": "string", "minLength": 1 },
					"quantity": { "type": "integer", "minimum": 1 },
					"unit_price": { "type": "number", "exclusiveMinimum": 0 },
					"unit_weight": { "type": "number", "exclusiveMinimum": 0 }
				},
				"required": ["sku", "name", "quantity", "unit_price", "unit_weight"]
			}
		},
		"notes": { "type": "string" }
	},
	"required": ["external_order_id", "recipient", "items"]
}
JSON;
