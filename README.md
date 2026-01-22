# MediTax Connect

A modern SaaS platform connecting healthcare professionals (doctors, dentists, pharmacies) with certified accountants for seamless tax management and financial services.

## Features

### For Healthcare Professionals
- **Profile Management**: Manage your practice information and credentials
- **Document Upload**: Securely upload income, expenses, and invoices (PDF, images)
- **Find Accountants**: Browse and select from verified accountants
- **Financial Dashboard**: View estimated yearly profit, tax calculations, and reports
- **Tax Reports**: Download financial summaries and tax reports

### For Accountants
- **Client Management**: View and manage assigned healthcare clients
- **Document Access**: Access client uploaded documents for tax preparation
- **Subscription System**: $80/month flat rate with unlimited clients
- **Verification Badge**: Get verified to appear in client searches
- **Tax Report Generation**: Create and send financial summaries to clients

### For Administrators
- **User Management**: View, edit, and verify users
- **Commission Tracking**: Monitor platform commissions (12% per contract)
- **Platform Analytics**: View subscription stats and platform metrics

## Tech Stack

- **Backend**: Pure PHP (no framework)
- **Frontend**: HTML, Tailwind CSS (CDN), Vanilla JavaScript
- **Database**: PostgreSQL
- **Authentication**: PHP Sessions with password hashing

## Project Structure

```
/
├── index.php              # Main router
├── includes/
│   ├── db.php            # Database connection & schema
│   ├── auth.php          # Authentication functions
│   ├── functions.php     # Helper functions
│   ├── header.php        # HTML header template
│   ├── nav.php           # Navigation template
│   └── footer.php        # Footer template
├── pages/
│   ├── landing.php       # Homepage
│   ├── login.php         # Login page
│   ├── register.php      # Registration page
│   ├── dashboard.php     # User dashboard
│   ├── profile.php       # Profile settings
│   ├── documents.php     # Document management
│   ├── accountants.php   # Find accountants
│   ├── clients.php       # Client management (accountants)
│   ├── subscription.php  # Subscription management
│   ├── reports.php       # Financial reports
│   ├── admin.php         # Admin dashboard
│   ├── admin-users.php   # User management
│   ├── admin-commissions.php # Commission tracking
│   └── 404.php           # Not found page
├── api/
│   ├── auth.php          # Auth API endpoints
│   ├── documents.php     # Document upload/delete
│   ├── financial.php     # Financial records API
│   ├── accountant.php    # Accountant selection
│   ├── client.php        # Client management
│   ├── subscription.php  # Subscription API
│   └── admin.php         # Admin API
├── uploads/
│   └── documents/        # Uploaded files
├── seed.php              # Database seeder
└── README.md
```

## Setup Instructions

### Prerequisites
- PHP 8.0 or higher
- PostgreSQL database

### Installation

1. **Database Setup**
   The database tables are automatically created on first run.

2. **Seed Demo Data**
   ```bash
   php seed.php
   ```

3. **Start the Server**
   ```bash
   php -S 0.0.0.0:5000
   ```

4. **Access the Application**
   Open your browser to `http://localhost:5000`

## Demo Accounts

After running the seeder:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@meditax.com | admin123 |
| Accountant | john.smith@accounting.com | password123 |
| Doctor | dr.james@clinic.com | password123 |
| Dentist | dr.emily@dental.com | password123 |
| Pharmacy | pharmacy@rxcare.com | password123 |

## Pricing Model

- **Healthcare Professionals**: FREE platform access
  - Pay accountant fees directly
  
- **Accountants**: $80/month subscription
  - Unlimited client management
  - 12% platform commission on contracts

## Security Features

- Password hashing with `password_hash()` / `password_verify()`
- Session-based authentication
- CSRF token protection
- Input sanitization
- Role-based access control
- Secure file uploads with validation

## Notes

- This is an MVP demonstration. Tax calculations use placeholder rates (25%).
- Payment processing is simulated (no real transactions).
- In production, integrate with a payment gateway (Stripe, PayPal).
- Document storage should use cloud storage (AWS S3, etc.) for scalability.

## Extending the Application

The codebase is designed for easy extension:

1. **Add New Pages**: Create PHP file in `/pages/` and add route in `index.php`
2. **Add API Endpoints**: Create handler in `/api/` and add route in `index.php`
3. **Database Changes**: Modify schema in `includes/db.php`
4. **Styling**: Uses Tailwind CSS via CDN - extend in header.php

## License

MIT License - Feel free to use and modify for your projects.
