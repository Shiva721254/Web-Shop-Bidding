# Frontend

A Vue 3 auction frontend built with Vite, Tailwind CSS, and Storybook.

## Frontend Layout

- `src/App.vue` and `src/main.js` bootstrap the application.
- `src/components` follows Atomic Design and contains the reusable UI system.
- `src/utils/api.js` centralizes API requests.
- `src/config.js` stores frontend runtime configuration.

See [src/components/README.md](src/components/README.md) for the component structure.

## Start

```bash
npm install
npm run dev
```

## Other Commands

```bash
npm run build
npm run storybook
npm run build-storybook
```

Set a different API endpoint with:

```env
VITE_API_DOMAIN=http://localhost
```
