# Auctions API

A PHP REST API for managing auction listings. The starter uses JSON storage so the domain flow is easy to inspect.

## Backend Layout

- `app/public/index.php` — HTTP entry point and route definitions.
- `app/src` — controllers, framework helpers, models, repositories, services, and utilities.
- `app/src/data/auctions.json` — current demo data store.
- `docker-compose.yml`, `nginx.conf`, `PHP.Dockerfile` — local container setup.

## API

- `GET /auctions`
- `GET /auctions/{id}`
- `POST /auctions`
- `PUT /auctions/{id}`
- `DELETE /auctions/{id}`

Create and update requests use `Content-Type: application/json`.

## Start

```bash
docker compose up
docker compose exec php composer install
```

The API is available at `http://localhost`. phpMyAdmin is available at `http://localhost:8080`.
