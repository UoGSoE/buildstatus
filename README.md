> [!NOTE]
> This project is a day-one prototype and remains in active early development. Expect breaking changes while the core domain takes shape.

# Build Status

Build Status is a Laravel + Livewire application that helps lab management teams monitor the operating-system build and maintenance status of university machines across multiple locations. The long-term goal is to provide at-a-glance insights for support staff while exposing a simple integration surface for automated build pipelines.

## Overview

- **Audience:** Operations staff who need to track machine provisioning progress and leadership teams who require high-level visibility.
- **Problem:** Spreadsheets and ad-hoc tooling make it hard to understand where lab machines sit in their build lifecycle.
- **Solution:** A central dashboard that ingests machine updates via API, stores change history, and presents live status cards with drill-down details.

## Current Capabilities

- Live dashboard listing lab machines with status, lab assignment, and recent update time.
- Modal and dedicated detail pages showing machine metadata plus paginated log history.
- REST API endpoint (Sanctum-protected) for upstream systems to push machine updates.
- Automatic lab creation on first sighting of a new lab name.
- Background job queue (Laravel Horizon ready) to process machine updates and persist log entries.

## Roadmap Snapshot

- Simulated data ingest command for local testing.
- Administrative tooling for managing labs, machine notes, and retention policies.
- Broader API surface and webhook integrations.
- Expanded automated test coverage and CI gating.

## Getting Started (Developers)

These instructions assume familiarity with Laravel tooling and that you have Docker + Lando available locally.

### 1. Clone & Configure

```bash
git clone https://github.com/UoGSoE/buildstatus
cd buildstatus
cp .env.example .env
```

### 2. Install Dependencies

```bash
composer install
npm install
npm run build
```

### 3. Boot the Lando Stack

```bash
lando start
```

### 4. Provision Demo Data

```bash
lando mfs
```

The `mfs` tooling command will drop, migrate, and seed the database with sample labs, machines, logs, and a preconfigured admin user (`admin2x@example.com`).

### 5. Access the App

- Web: http://buildstatus.lndo.site (or the URL provided by `lando info`).
- Horizon: `lando horizon`.
- Tests: `lando test` (runs Pest in parallel).

If UI changes are not visible, run `npm run dev` (either on the host or via the `lando npmd` tooling command).

## Development Workflow

- **Queues:** Machine updates are dispatched to the `MachineUpdate` queued job; ensure a queue worker (Horizon or `php artisan queue:work`) is running when consuming live data.
- **Livewire Components:** Dashboard (`App\Livewire\MachineList`) and detail view (`App\Livewire\MachineDetails`) power the UI, using Flux UI Pro components for styling.
- **Authentication:** Keycloak SSO via Socialite, with optional local login when SSO is disabled. Access is restricted to authenticated users.
- **Configuration:** Core settings live in `config/sso.php` and `config/buildstatus.php` (base domain for hostname shortening).

## API

`POST /api/machine`

- **Auth:** Sanctum token (`auth:sanctum`).
- **Payload:**
  - `name` (string, required)
  - `ip_address`, `status`, `notes`, `lab_name` (optional strings)
- **Behavior:** Dispatches a queued job that upserts machine metadata, creates labs on demand, and records a JSON log entry.

## Testing

- Pest v4 with RefreshDatabase trait powers the feature suites.
- Coverage includes API validation, queued job behavior, and Livewire component rendering.
- Run targeted suites with `lando test --filter=MachineUpdateTest` or similar.

## Tech Stack

- Laravel 12, PHP 8.4
- Livewire 3 + Flux UI Pro components
- Sanctum authentication, Horizon queue monitoring
- MySQL (default), Redis cache/session via Lando
- Tailwind CSS 4 with Vite bundling

## Contributing

As the project stabilizes, we will publish contribution guidelines. For now, coordinate directly with the core team before opening pull requests—core domain decisions are still in flux.

