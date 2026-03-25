# MediTax Connect

A SaaS platform connecting healthcare professionals (doctors, dentists, pharmacies) with certified accountants for seamless tax management and financial services.

## Tech Stack

- **Backend**: Pure PHP 8.2 (no framework)
- **Frontend**: HTML, Tailwind CSS (CDN), Vanilla JavaScript
- **Database**: PostgreSQL (Replit built-in, via PGHOST/PGUSER/etc. env vars)
- **Authentication**: PHP Sessions with password hashing

## Project Structure

```
/
├── index.php              # Main router (front controller)
├── includes/
│   ├── db.php             # Database connection & schema initialization
│   ├── auth.php           # Authentication functions
│   ├── functions.php      # Helper functions
│   ├── header.php         # HTML header template
│   ├── nav.php            # Navigation template
│   └── footer.php         # Footer template
├── pages/                 # Page view files
├── api/                   # API endpoint handlers
├── uploads/documents/     # User-uploaded files
└── seed.php               # Database seeder
```

## Running the App

```bash
php -S 0.0.0.0:5000 index.php
```

The workflow "Start application" is configured to run this command on port 5000.

## Database Setup

The schema is auto-created on startup via `initializeDatabase()` in `includes/db.php`.
Seed demo data with:

```bash
php seed.php
```

## Demo Accounts

| Role       | Email                        | Password    |
|------------|------------------------------|-------------|
| Admin      | admin@meditax.com            | admin123    |
| Accountant | john.smith@accounting.com    | password123 |
| Doctor     | dr.james@clinic.com          | password123 |
| Dentist    | dr.emily@dental.com          | password123 |
| Pharmacy   | pharmacy@rxcare.com          | password123 |

## Key Notes

- Database connection uses Replit env vars: `PGHOST`, `PGPORT`, `PGDATABASE`, `PGUSER`, `PGPASSWORD`
- `index.php` is the front controller — all requests route through it
- `includes/db.php` creates all tables (PostgreSQL-compatible DDL) on first run
- File uploads go to `uploads/documents/`
- Tax calculations use 25% placeholder rate (MVP)
- Payment processing is simulated (no real transactions)
