# SHINE Ordering System API

Open in browser after copying project to XAMPP `htdocs`:

```txt
http://localhost/realtime_product_ordering_system/api/
```

Browser API tester:

```txt
http://localhost/realtime_product_ordering_system/api/tester.php
```

## Products

- `GET /api/products.php` - list products
- `GET /api/products.php?id=1` - product details
- `POST /api/products.php` - create product
- `PUT /api/products.php?id=1` - update product
- `DELETE /api/products.php?id=1` - delete product

Create product JSON:

```json
{
  "name": "New Burger",
  "description": "API created product",
  "category": "Burgers",
  "price": 120,
  "stock": 20,
  "status": "available",
  "image": "default-food.png"
}
```

## Orders

- `GET /api/orders.php` - list orders
- `GET /api/orders.php?id=1` - order with items
- `POST /api/orders.php` - create order
- `PUT /api/orders.php?id=1` - update order status
- `DELETE /api/orders.php?id=1` - delete order

Create order JSON:

```json
{
  "user_id": 3,
  "items": [
    {"product_id": 1, "quantity": 2},
    {"product_id": 2, "quantity": 1}
  ]
}
```

Update order status JSON:

```json
{"status":"preparing"}
```

Allowed status: `pending`, `preparing`, `ready`, `completed`, `cancelled`.

## Users

- `GET /api/users.php`
- `GET /api/users.php?id=1`
- `POST /api/users.php`
- `PUT /api/users.php?id=1`
- `DELETE /api/users.php?id=1`

Create user JSON:

```json
{
  "name": "Juan Customer",
  "email": "juan@example.com",
  "password": "123456",
  "role": "customer"
}
```

## Browser access notes

Normal browser address bar can only easily do GET. For POST, PUT, PATCH, and DELETE, use:

- `api/tester.php`
- Postman
- JavaScript fetch
- PHP/cURL

This API uses the same database config from `config/db.php`.
