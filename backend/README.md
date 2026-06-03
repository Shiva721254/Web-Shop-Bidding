# Backend — Web Shop with Buying & Bidding

A PHP 8.3 REST API following MVC architecture, backed by MariaDB, and containerised with Docker + Nginx.

## Stack

- PHP 8.3 (FPM) + Nginx
- MariaDB (via Docker)
- Composer with PSR-4 autoloading
- `nikic/fast-route` for routing
- Custom HS256 JWT authentication (no third-party auth library)

## Project Layout

```
backend/
├── app/
│   ├── database/
│   │   ├── schema.sql          # Table definitions (auto-loaded by Docker)
│   │   └── seeds.sql           # Demo users, products, and auctions
│   ├── public/
│   │   └── index.php           # Entry point — route definitions and dispatcher
│   └── src/
│       ├── Controllers/        # HTTP request handling
│       ├── Framework/          # Auth, Database, base Controller
│       ├── Models/             # Plain data classes
│       ├── Repositories/       # SQL queries (interface + implementation)
│       ├── Services/           # Business logic (interface + implementation)
│       └── Utils/              # JsonStore helper (legacy)
├── docker-compose.yml
├── nginx.conf
└── PHP.Dockerfile
```

## Start

```bash
docker compose up -d
docker compose exec php composer install
```

- API: `http://localhost`
- phpMyAdmin: `http://localhost:8080`

## Demo Credentials

| Role     | Email                 | Password   |
|----------|-----------------------|------------|
| Admin    | admin@shop.com        | password   |
| Customer | customer@shop.com     | password   |

## API Endpoints

All endpoints accept and return `application/json`.  
Protected endpoints require `Authorization: Bearer <token>`.

### Auth

| Method | Path             | Auth     | Description                        |
|--------|------------------|----------|------------------------------------|
| POST   | /auth/login      | —        | Login; returns JWT token + user    |
| POST   | /auth/register   | —        | Register; returns JWT token + user |

### Products

| Method | Path             | Auth     | Description                                         |
|--------|------------------|----------|-----------------------------------------------------|
| GET    | /products        | —        | List products (filter: `type`, `category`; paginate: `page`, `limit`) |
| GET    | /products/{id}   | —        | Get single product                                  |
| POST   | /products        | Admin    | Create product; returns new object with id          |
| PUT    | /products/{id}   | Admin    | Update product                                      |
| DELETE | /products/{id}   | Admin    | Delete product                                      |

### Auctions

| Method | Path                          | Auth     | Description                                                   |
|--------|-------------------------------|----------|---------------------------------------------------------------|
| GET    | /auctions                     | —        | List auctions (filter: `status`, `category`; paginate: `page`, `limit`) |
| GET    | /auctions/{id}                | —        | Get single auction                                            |
| POST   | /auctions                     | Admin    | Create auction; returns new object with id                    |
| PUT    | /auctions/{id}                | Admin    | Update auction                                                |
| DELETE | /auctions/{id}                | Admin    | Delete auction                                                |
| GET    | /auctions/{id}/bids           | —        | List bids for auction (paginate: `page`, `limit`)             |
| POST   | /auctions/{id}/bids           | Customer | Place bid; returns new bid with id                            |

### Orders

| Method | Path                          | Auth     | Description                                         |
|--------|-------------------------------|----------|-----------------------------------------------------|
| GET    | /orders                       | Admin    | List all orders (paginate: `page`, `limit`)         |
| GET    | /orders/{id}                  | Auth     | Get single order (own order or admin)               |
| POST   | /orders                       | Auth     | Create order; returns new object with id            |
| PUT    | /orders/{id}/status           | Admin    | Update order status                                 |
| GET    | /users/{userId}/orders        | Auth     | List orders for a user (own orders or admin)        |

### Error Responses

Every endpoint returns a JSON error body on failure:

```json
{ "error": "Human-readable message" }
```

Common status codes: `400` Bad Request · `401` Unauthorized · `403` Forbidden · `404` Not Found · `422` Validation Error · `500` Internal Server Error.

## Environment Variables

| Variable         | Default | Description                              |
|------------------|---------|------------------------------------------|
| `ALLOWED_ORIGIN` | `''`    | Frontend origin for CORS (e.g. `https://your-app.example.com`) |
