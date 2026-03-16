# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Tontine management application (rotating savings groups) targeting West Africa (Côte d'Ivoire). Built with Laravel 12, Livewire 4.1, Alpine.js, and Tailwind CSS 4.0 with dark mode. UI labels are in French. Currency is FCFA.

## Commands

```bash
# Development server (concurrent: PHP server + Vite + queue worker)
composer dev

# First-time setup
composer setup

# Build frontend assets
npm run build

# Run tests (PHPUnit, uses in-memory SQLite)
php artisan test

# Run a single test file
php artisan test --filter=ExampleTest

# Code formatting
./vendor/bin/pint

# Database
php artisan migrate
php artisan db:seed

# Scheduled commands (run manually or via scheduler)
php artisan contributions:send-reminders
php artisan contributions:mark-late
php artisan tours:fail-expired
```

## Architecture

### Stack
- **Backend:** Laravel 12 + Livewire 4.1
- **Frontend:** Alpine.js + Tailwind CSS 4.0 (dark mode) + Vite 7
- **Database:** SQLite (default), MySQL supported
- **PDF:** barryvdh/laravel-dompdf
- **Push Notifications:** minishlink/web-push (requires VAPID keys)
- **Phone Validation:** propaganistas/laravel-phone

### Domain Model

The core domain flow is: **User → Tontine → Tour → Contribution → PaymentProof**

- A **Tontine** is a savings group with members who contribute a fixed amount on a schedule (weekly/biweekly/monthly)
- **TontineMember** is the pivot with role (ADMIN/TREASURER/MEMBER), position, and `parts` (contribution multiplier)
- **Tours** are rounds where one member (beneficiary) receives the pot. Generated round-robin via `TontineService::generateTours()`
- **Contributions** track each member's payment per tour. Status flow: PENDING → DECLARED → CONFIRMED (or REJECTED/LATE)
- **PaymentProof** stores payment evidence (Orange Money, MTN MoMo, Wave, cash, bank transfer)

### Key Services

- **ContributionService** — declare/confirm/reject contributions, auto-complete tours, late counting
- **TontineService** — generate tours with round-robin scheduling, aggregate stats
- **NotificationService** — multi-channel notifications (database + email + web push)

### Authorization Pattern

```php
auth()->user()->canManage($tontine)  // admin OR treasurer role in tontine
auth()->user()->is_admin             // system-wide admin
```

Middleware stack: `TontineMemberMiddleware` (membership check, admins bypass), `TontineRoleMiddleware` (role check), `AdminMiddleware` (system admin), `ForcePasswordChange`.

### Routes Structure

All tontine routes are nested under `tontines/{tontine}/` with `tontineMemberMiddleware`. Admin routes under `/admin` with `AdminMiddleware`. See `routes/web.php`.

### Enums

Located in `app/Enums/`. All use **UPPERCASE cases** (e.g., `TontineStatus::ACTIVE`, `ContributionStatus::DECLARED`). Each enum has `label()` and `color()` methods. Values are lowercase strings.

### Livewire Components

Located in `app/Livewire/`. Key components: `ContributionDeclare` (member payment self-declaration with file upload), `TontineChat` (messaging), `NotificationBell`, `MessageBell`.

### UI Conventions

- Layout: `<x-layouts.app>` with `<x-slot:header>`
- Card style: `bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700`
- Modals: `<x-modal id="name" maxWidth="lg">`, trigger via `$dispatch('open-modal-name')`
- Blade components: `x-badge`, `x-button`, `x-input`, `x-select`, `x-textarea`, `x-file-upload`, `x-alert`
- Flash messages: `session('success')` / `session('error')` rendered with `<x-alert>`

### Activity Logging

```php
ActivityLog::log('action', $subject, userId: $id, tontineId: $id);
```

Polymorphic subject tracking for audit trails on all domain events.

### File Uploads

Payment proof screenshots stored via `store('payment-proofs', 'public')`, accessed with `Storage::url()`.

### Tontine Settings

JSON column on `tontines` table. Access via `$tontine->getSetting('key', default)` / `setSetting('key', value)`. Controls late detection, penalties, auto-exclusion, reminders, and tour failure grace periods.
