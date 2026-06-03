# Web Shop with Buying & Bidding

A full-stack web shop where customers can buy products at a fixed price or place bids on auction items. Built with Vue 3 (frontend) and PHP 8.3 (REST API backend), backed by MariaDB.

## Project Layout

- [backend/README.md](backend/README.md) — API endpoints, Docker setup, demo credentials.
- [frontend/README.md](frontend/README.md) — frontend setup, scripts, and component guide.

## Quick Start

**Backend**

```bash
cd backend
docker compose up -d
docker compose exec php composer install
```

API available at `http://localhost` · phpMyAdmin at `http://localhost:8080`

**Frontend**

```bash
cd frontend
npm install
npm run dev
```

Frontend available at `http://localhost:5173`

## Demo Credentials

| Role     | Email                 | Password   |
|----------|-----------------------|------------|
| Admin    | admin@shop.com        | password   |
| Customer | customer@shop.com     | password   |

## Features

**Customer**
- Browse buy-now products and auction listings
- Add items to cart and place orders
- Place bids on active auctions with bid validation
- View order history and auction status

**Admin**
- Create, edit, and delete products (buy-now or auction type)
- Manage orders and update order status
- Monitor auctions and bidding activity

**Technical**
- Vue 3 SPA with Vue Router and Pinia state management
- PHP REST API following MVC pattern with PSR-4 namespaces and autoloading
- JWT authentication with role-based authorization (admin / customer)
- Tailwind CSS with responsive design (mobile → desktop)
- GET endpoints support filtering and pagination
