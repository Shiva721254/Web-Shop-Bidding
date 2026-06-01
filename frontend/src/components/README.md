# Components

This directory follows Atomic Design and groups the reusable UI system for the frontend.

## Levels

- `atoms/` — small building blocks such as `Button`, `Badge`, `Heading`, `Text`, and `DateDisplay`.
- `molecules/` — simple combinations such as `AuctionMeta` and `CategoryBadge`.
- `organisms/` — larger sections such as `AuctionCard`, `AuctionDetail`, `Header`, and `Footer`.
- `templates/` — layout-level structures such as `AuctionArchive`.
- `pages/` — route-level views such as `AuctionArchivePage`.

## Guidelines

1. Start with atoms, then compose molecules, organisms, templates, and pages.
2. Keep components reusable and focused on a single responsibility.
3. Prefer props and slots over hardcoded content.

## Folder Notes

Each subfolder has a short local README with folder-specific context.
