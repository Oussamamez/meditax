# MediTax Connect

A SaaS platform connecting healthcare professionals with accountants for tax management.

## Overview

MediTax Connect is a pure PHP web application that helps doctors, dentists, and pharmacies connect with certified accountants for managing their yearly taxes and financial obligations.

## Tech Stack

- **Backend**: Pure PHP (no framework)
- **Frontend**: HTML + Tailwind CSS (CDN) + Vanilla JavaScript
- **Database**: PostgreSQL
- **Authentication**: PHP Sessions with password hashing

## Project Structure

```
/
├── index.php           # Main router
├── includes/           # Core PHP includes (db, auth, functions)
├── pages/              # Page templates
├── api/                # AJAX API endpoints
├── uploads/documents/  # User uploaded files
├── seed.php            # Database seeder with demo data
└── README.md           # Documentation
```

## Running the Application

The app runs on PHP's built-in server on port 5000:
```bash
php -S 0.0.0.0:5000
```

## Demo Accounts

Run `php seed.php` to create demo data:

- **Admin**: admin@meditax.com / admin123
- **Accountant**: john.smith@accounting.com / password123
- **Doctor**: dr.james@clinic.com / password123

## Key Features

1. **Landing Page**: Professional marketing page with pricing
2. **Authentication**: Register/Login with role selection
3. **Healthcare Dashboard**: Upload documents, select accountants, view tax estimates
4. **Accountant Dashboard**: Manage clients, subscription ($80/mo), verify status
5. **Admin Panel**: User management, commission tracking (12%)

## Database Tables

- `users` - All users with role-based access
- `subscriptions` - Accountant subscription status
- `accountant_clients` - Client-accountant relationships
- `documents` - Uploaded files metadata
- `financial_records` - Income/expense tracking per year
- `tax_reports` - Generated tax reports
- `commissions` - Platform commission tracking
- `messages` - User messaging

## Recent Changes

- Initial build: Full PHP application with all core features
- Database: PostgreSQL with auto-initialization
- UI: Professional blue/green medical-financial theme

## User Preferences

- Pure PHP, no frameworks
- Tailwind CSS via CDN
- Vanilla JavaScript for interactivity
- Clean, professional design
