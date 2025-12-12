# Challenge Urbano Express

## Setup

Copy `.env.example` to `.env`. It shouldn't be necessary to change any variable.
Both the app and docker-compose.yml read from the same `.env` file.

```bash
cp .env.example .env
docker-compose up -d
```

Install composer dependencies:

```bash
docker exec -it urbano-express-php-1 composer install
```

Create DB schema:

```bash
docker exec -it urbano-express-php-1 php /doctrine orm:schema-tool:create

# Update db schema if necessary
docker exec -it urbano-express-php-1 php /doctrine orm:schema-tool:update --force
```

API runs in <http://localhost:8080>. The port can be changed in the `.env` file.

## Testing

The testing framework used is Pest. To run the tests suite run any of the
following commands:

```bash
# Using composer script
docker exec -it urbano-express-php-1 composer test

# or run pest directly
docker exec -it urbano-express-php-1 ./vendor/bin/pest
```

> For the sake of brevity I only covered the most important core logic with unit
> tests. But of course, ideally, all should be covered.

## User creation

Users can be created running the following command:

```bash
docker exec -it urbano-express-php-1 ./app user:create UserName user@email.com
```

This command will return the user id and access token (randomly generated).

## Test client CLI script

I included a simple CLI client script in node that can communicate with the API.

### Setup

The setup is pretty simple, it only needs the dependencies to be installed:

```bash
pnpm install
```

### Usage

The script itself includes all the necessary information to be run, the help is
pretty self-explanatory:

```bash
# Top-level help
pnpm client -h

# Help by command:
pnpm client order:create -h
pnpm client order:list -h
pnpm client order:get -h
```

All 3 commands require the api token to be passed as the first parameter.

```bash
pnpm client order:list <access-token>
# e.g,:
pnpm client order:list b849d9d4439fd7befed65ed6fcd5ebdb
```

The `order:create` command generates a random external order ID to avoid clashes,
and replaces it in the default (or any) json body. But a custom one can be
passed with the `-e` or `--external-order-id` option:

```bash
pnpm client order:create <access-token> -e <externalOrderId>
# e.g,:
pnpm client order:create b849d9d4439fd7befed65ed6fcd5ebdb -e ext-id-123
```

I included a default JSON file in the `./client` directory. But a custom one can
be passed to the command if necessary:

```bash
pnpm client order:create <access-token> [json-file]
# e.g,:
pnpm client order:create b849d9d4439fd7befed65ed6fcd5ebdb ./path/to/file.json
```

## Endpoints

If using VS Code with the REST Client extension, you should be able to run these
requests directly from here. Just make sure you change the bearer token to the
correct one for your user, or change your user's token for the one in this docs
directly in the database.

### `POST /orders`

Create an order.

```http
# @prompt externalOrderId The external order id to use for this request
POST http://localhost:8080/orders
Content-Type: application/json
Authorization: Bearer b849d9d4439fd7befed65ed6fcd5ebdb

{
  "external_order_id": "{{externalOrderId}}",
  "notes": "Some optional notes.",
  "recipient": {
    "name": "John Doe",
    "address_1": "Calle Falsa 123",
    "address_2": "Planta Alta",
    "city": "San Isidro",
    "state": "Buenos Aires",
    "postal_code": "1640"
  },
  "items": [
    {
      "sku": "ujsdfh823nfd8",
      "name": "Product A",
      "quantity": 2,
      "unit_price": 1500.0,
      "unit_weight": 125.0
    },
    {
      "sku": "ij390jfd9034s",
      "name": "Product B",
      "quantity": 1,
      "unit_price": 20000.0,
      "unit_weight": 520.0
    }
  ]
}
```

**Example responses:**

Success:

```json
{
  "message": "Order created",
  "order_id": "019b13f3-35da-7397-a058-f34df4d3ff92"
}
```

Error:

```json
{
  "error": "Order with external ID 'ext-id-123' already exists."
}
```

Request validation errors (missing or invalid fields):

```json
{
  "error": "Invalid request",
  "validation_errors": {
    "/recipient": [
      "The required properties (city) are missing"
    ],
    "/items/0/quantity": [
      "Number must be greater than or equal to 1"
    ]
  }
}
```

### `GET /orders/{orderId}`

Shows order data for a specific order by ID.

```http
# @prompt orderId
GET http://localhost:8080/orders/{{orderId}}
Content-Type: application/json
Authorization: Bearer b849d9d4439fd7befed65ed6fcd5ebdb
```

**Example response:**

Success:

```json
{
  "order": {
    "id": "019b13f1-385f-714f-a3d2-5088c2735024",
    "customerId": "019b1333-604b-7080-8305-1be8591a94c5",
    "externalOrderId": "ext-id-123",
    "status": "created",
    "notes": "Some optional notes.",
    "totalPrice": 23000,
    "totalWeight": 770,
    "recipient": {
      "name": "John Doe",
      "phone": null,
      "email": null,
      "address_1": "Calle Falsa 123",
      "address_2": "Planta Alta",
      "city": "San Isidro",
      "postalCode": "1640",
      "full_address": "Calle Falsa 123, San Isidro, Buenos Aires 1640"
    },
    "items": [
      {
        "id": "019b13f1-385f-714f-a3d2-5088c2b0c466",
        "sku": "ujsdfh823nfd8",
        "name": "Product A",
        "quantity": 2,
        "unit_price": 1500,
        "total_price": 3000,
        "unit_weight": 125,
        "total_weight": 250,
        "created_at": "2025-12-12T19:02:14+00:00",
        "updated_at": "2025-12-12T19:02:14+00:00"
      },
      {
        "id": "019b13f1-385f-714f-a3d2-5088c3168a53",
        "sku": "ij390jfd9034s",
        "name": "Product B",
        "quantity": 1,
        "unit_price": 20000,
        "total_price": 20000,
        "unit_weight": 520,
        "total_weight": 520,
        "created_at": "2025-12-12T19:02:14+00:00",
        "updated_at": "2025-12-12T19:02:14+00:00"
      }
    ],
    "created_at": "2025-12-12T19:02:14+00:00",
    "updated_at": "2025-12-12T19:02:14+00:00"
  }
}
```

Request validation error (orderId is not a UUID):

```json
{
  "error": "Invalid request",
  "validation_errors": {
    "/orderId": [
      "The data must match the 'uuid' format"
    ]
  }
}
```

### `GET /orders`

This endpoint lists all orders for the authenticated user.

```http
GET http://localhost:8080/orders
Content-Type: application/json
Authorization: Bearer b849d9d4439fd7befed65ed6fcd5ebdb
```

**Example response:**

```jsonc
{
  "orders": [
    // Order objects each with the same shape as the endpoint above.
    // Or empty array if the user has no orders.
  ]
}
```

## Architecture Decisions

I used a DDD approach, even thought the domain of the challenge was small.

I opted to divide the domain in two bounded contexts and a shared module:

- Shipping
- User (it could also have been called Customer)
- Shared

The Shipping module, as the name implies, is in charge of anything related to
shipping order management. In this simple example there isn't much domain logic,
but I included an example of how to handle order state transitions.

The User module for this challenge is just a supporting subdomain used mainly
for authentication. But eventually could be used for user/customer management in
general as well.

The Shared module has anything that needs to be shared between modules, which
for this challenge isn't much except for the `Timestampable` trait and the
`Email` Value Object. Besides that, it is also in charge of defining app
bootstrapping contracts and main logic.

I didn't include a relation between Order an User because, for one, I didn't
need it, and second, I didn't want cross-context contamination. If it were
necessary there are strategies, even using Doctrine, to handle this separation
by defining a "local" Shipping "Customer" interface that the User module will
implement, effectively inverting the dependency. [See](https://www.doctrine-project.org/projects/doctrine-orm/en/3.5/cookbook/resolve-target-entity-listener.html).

I tried to orient the domain modeling around a logistics business with
e-commerce clients. This is why I only included relevant order item information,
and an external order id.

I decided to include Recipient data as a Value Object on Order to simplify, but
ideally it could be its own entity.

I also went with a simple Order Status model, with just a few states, but more
could be added if necessary (e.g,: "pending_pickup", "exception", "returned").
