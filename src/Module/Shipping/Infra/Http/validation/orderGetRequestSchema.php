<?php

return <<<'JSON'
{
	"type": "object",
	"properties": {
		"orderId": { "type": "string", "format": "uuid" }
	},
	"required": ["orderId"]
}
JSON;
