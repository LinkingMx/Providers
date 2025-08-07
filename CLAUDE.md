# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 Provider Management System with Filament admin panels for managing business provider registrations, document compliance, and administrative workflows. The application uses a multi-panel architecture with separate interfaces for administrators and providers.

## Essential Commands

### Development
```bash
composer dev              # Run all services concurrently (server, queue, logs, Vite)
php artisan serve         # Start development server only
npm run dev              # Watch and compile frontend assets
php artisan queue:work   # Process background jobs (required for emails)
php artisan pail         # Real-time log monitoring
```

### Testing
```bash
composer test            # Run full test suite with config clearing
php artisan test        # Run PHPUnit tests directly
php artisan test --filter TestName  # Run specific test
```

### Build & Deploy
```bash
npm run build            # Production asset build
php artisan migrate      # Run database migrations
php artisan db:seed      # Seed database with initial data
php artisan config:cache # Cache configuration for production
php artisan filament:upgrade  # Update Filament assets after package updates
```

### Email Testing
```bash
php artisan mail:test smtp email@example.com  # Test SMTP configuration
php artisan mail:check-config                 # Verify mail configuration
```

## Architecture & Key Patterns

### Multi-Panel Structure
The application uses Filament's multi-panel architecture:
- **Admin Panel** (`/admin`): Full system administration at `app/Filament/Resources/`
- **Provider Panel** (`/provider`): Provider self-service at `app/Providers/Filament/`

### Core Models & Relationships
```
User → ProviderProfile (one-to-one)
User ↔ DocumentType (many-to-many via ProviderDocument pivot)
User ↔ Branch (many-to-many with pivot data)
DocumentType ↔ ProviderType (many-to-many)
```

### Key Directories
- `app/Filament/Resources/`: Admin panel CRUD resources
- `app/Models/`: Eloquent models with complex relationships
- `app/Console/Commands/`: Custom artisan commands for maintenance
- `app/Jobs/`: Background jobs for email and document processing
- `app/Observers/`: Model lifecycle hooks (e.g., auto-assign documents on provider creation)
- `database/migrations/`: Database schema definitions

### Document Management Flow
1. Provider registers → Observer triggers document assignment based on provider type
2. Documents tracked via `ProviderDocument` pivot with status and expiration
3. Background jobs handle email notifications and status updates
4. Admin reviews and approves through Filament resources

### Testing Approach
- Uses PestPHP for testing (`tests/` directory)
- Database transactions for test isolation
- Factory patterns for test data generation

## Important Context

### Queue Processing
Background jobs are essential for:
- Email delivery (welcome emails, notifications)
- Document assignment and cleanup
- Status workflow transitions

Always ensure `php artisan queue:work` is running during development.

### Filament Customization
- Resources define CRUD operations with form schemas and table configurations
- Widgets provide dashboard analytics
- Custom pages extend functionality beyond CRUD
- Shield integration manages role-based permissions

### Email System
- Custom mail commands for testing SMTP configuration
- Markdown templates in `resources/views/mail/`
- Queued delivery for performance

### Database
- Default SQLite configuration (easily switchable to MySQL/PostgreSQL)
- Complex pivot tables for document management
- Soft deletes on critical models

## Working with Provider Documents
The document system is central to the application:
- `DocumentType` defines required documents per provider type
- `ProviderDocument` tracks submission, status, and expiration
- `DocumentStatus` manages workflow states
- Observers automatically assign documents when providers register

When modifying document logic, consider:
1. Impact on existing provider documents
2. Status transition rules
3. Expiration date handling
4. Email notification triggers