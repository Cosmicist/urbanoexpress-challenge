import 'dotenv/config'
import { Command, program } from 'commander'
import { readFileSync } from 'node:fs'

type CommandAction = (this: Command, ...args: any[]) => void | Promise<void>

const createOrder: CommandAction = async function (apiToken: string, jsonFilePath: string) {
	const jsonFileContents = readFileSync(jsonFilePath, 'utf-8')
	if (!jsonFileContents) {
		console.error(`JSON file not found or empty at path: ${jsonFilePath}`)
		return
	}

	const createOrderRequest: any = JSON.parse(jsonFileContents)

	const { externalOrderId } = this.opts<{ externalOrderId?: string }>()
	if (externalOrderId) {
		createOrderRequest.external_order_id = externalOrderId
	} else {
		const randomId = Math.random().toString(36).substring(2, 10)
		createOrderRequest.external_order_id = `order-${randomId}`
	}

	const response = await fetch(`http://localhost:${process.env.HOST_PORT}/orders`, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			Authorization: `Bearer ${apiToken}`,
		},
		body: JSON.stringify(createOrderRequest),
	})

	const data = await response.json()
	console.log('Response Status:', response.status)
	console.log('Response Body:')
	console.dir(data, { depth: null })
}

const getOrder: CommandAction = async function (apiToken: string, orderId: string) {
	const response = await fetch(`http://localhost:${process.env.HOST_PORT}/orders/${orderId}`, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json',
			Authorization: `Bearer ${apiToken}`,
		},
	})

	const data = await response.json()
	console.log('Response Status:', response.status)
	console.log('Response Body:')
	console.dir(data, { depth: null })
}

const getOrderList: CommandAction = async function (apiToken: string) {
	const response = await fetch(`http://localhost:${process.env.HOST_PORT}/orders`, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json',
			Authorization: `Bearer ${apiToken}`,
		},
	})

	const data = await response.json()
	console.log('Response Status:', response.status)
	console.log('Response Body:')
	console.dir(data, { depth: null })
}

async function main() {
	program
		.command('order:create')
		.description('Create a new order with a random or specified External Order ID')
		.argument('<api-token>', 'API token for authentication')
		.argument(
			'[json-file]',
			'Path to JSON file for order creation',
			'./client/create-order-request.json',
		)
		.option(
			'-e --external-order-id <externalOrderId>',
			'External Order ID to associate with the order',
		)
		.action(createOrder)

	program
		.command('order:get')
		.description('Get order details by Order ID')
		.argument('<api-token>', 'API token for authentication')
		.argument('<order-id>', 'Order ID to retrieve')
		.action(getOrder)

	program
		.command('order:list')
		.description('List all orders for the authenticated customer')
		.argument('<api-token>', 'API token for authentication')
		.action(getOrderList)

	program.parse()
}

main().catch((error) => {
	console.error('Error:', error)
})
