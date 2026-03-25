-- MediTax Connect - Complete Database Setup
-- PostgreSQL SQL Script
-- Date: February 27, 2026

-- ============================================================
-- 1. CREATE DATABASE
-- ============================================================
CREATE DATABASE meditax;

-- Connect to the database:
-- \c meditax

-- ============================================================
-- 2. CREATE USERS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL CHECK (
        role IN (
            'doctor',
            'dentist',
            'pharmacy',
            'accountant',
            'admin'
        )
    ),
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    business_name VARCHAR(255),
    license_number VARCHAR(100),
    specialty VARCHAR(100),
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- 3. CREATE SUBSCRIPTIONS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS subscriptions (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users (id) ON DELETE CASCADE,
    status VARCHAR(50) DEFAULT 'inactive' CHECK (
        status IN (
            'active',
            'inactive',
            'cancelled'
        )
    ),
    plan_name VARCHAR(100) DEFAULT 'Professional',
    amount DECIMAL(10, 2) DEFAULT 80.00,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- 4. CREATE ACCOUNTANT-CLIENT RELATIONSHIPS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS accountant_clients (
    id SERIAL PRIMARY KEY,
    accountant_id INTEGER REFERENCES users (id) ON DELETE CASCADE,
    client_id INTEGER REFERENCES users (id) ON DELETE CASCADE,
    status VARCHAR(50) DEFAULT 'pending' CHECK (
        status IN (
            'pending',
            'active',
            'completed',
            'cancelled'
        )
    ),
    year INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (
        accountant_id,
        client_id,
        year
    )
);

-- ============================================================
-- 5. CREATE DOCUMENTS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS documents (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users (id) ON DELETE CASCADE,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    file_size INTEGER NOT NULL,
    category VARCHAR(100) CHECK (
        category IN (
            'income',
            'expense',
            'invoice',
            'tax_report',
            'other'
        )
    ),
    year INTEGER NOT NULL,
    description TEXT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- 6. CREATE FINANCIAL RECORDS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS financial_records (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users (id) ON DELETE CASCADE,
    year INTEGER NOT NULL,
    total_income DECIMAL(15, 2) DEFAULT 0,
    total_expenses DECIMAL(15, 2) DEFAULT 0,
    estimated_profit DECIMAL(15, 2) DEFAULT 0,
    estimated_tax DECIMAL(15, 2) DEFAULT 0,
    tax_rate DECIMAL(5, 2) DEFAULT 25.00,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (user_id, year)
);

-- ============================================================
-- 7. CREATE COMMISSIONS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS commissions (
    id SERIAL PRIMARY KEY,
    accountant_id INTEGER REFERENCES users (id) ON DELETE CASCADE,
    client_id INTEGER REFERENCES users (id) ON DELETE CASCADE,
    amount DECIMAL(10, 2) NOT NULL,
    commission_rate DECIMAL(5, 2) DEFAULT 12.00,
    commission_amount DECIMAL(10, 2) NOT NULL,
    year INTEGER NOT NULL,
    status VARCHAR(50) DEFAULT 'pending' CHECK (
        status IN (
            'pending',
            'paid',
            'cancelled'
        )
    ),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- 8. CREATE TAX REPORTS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS tax_reports (
    id SERIAL PRIMARY KEY,
    client_id INTEGER REFERENCES users (id) ON DELETE CASCADE,
    accountant_id INTEGER REFERENCES users (id) ON DELETE CASCADE,
    year INTEGER NOT NULL,
    status VARCHAR(50) DEFAULT 'draft' CHECK (
        status IN (
            'draft',
            'pending_review',
            'approved',
            'submitted'
        )
    ),
    total_income DECIMAL(15, 2),
    total_deductions DECIMAL(15, 2),
    taxable_income DECIMAL(15, 2),
    tax_liability DECIMAL(15, 2),
    summary TEXT,
    report_file VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- 9. CREATE MESSAGES TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS messages (
    id SERIAL PRIMARY KEY,
    sender_id INTEGER REFERENCES users (id) ON DELETE CASCADE,
    receiver_id INTEGER REFERENCES users (id) ON DELETE CASCADE,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- 10. CREATE AI FINANCIAL REPORTS TABLE (NEW)
-- ============================================================
CREATE TABLE IF NOT EXISTS ai_financial_reports (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users (id) ON DELETE CASCADE,
    accountant_id INTEGER REFERENCES users (id) ON DELETE CASCADE,
    year INTEGER NOT NULL,
    report_type VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    summary TEXT NOT NULL,
    detailed_analysis TEXT,
    recommendations TEXT,
    key_metrics TEXT,
    charts_data TEXT,
    status VARCHAR(50) DEFAULT 'draft' CHECK (
        status IN (
            'draft',
            'generated',
            'reviewed',
            'approved',
            'archived'
        )
    ),
    ai_model VARCHAR(100),
    generation_method VARCHAR(50) CHECK (
        generation_method IN (
            'automated',
            'manual_review',
            'template'
        )
    ),
    created_by INTEGER REFERENCES users (id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- 11. CREATE AI REPORT VERSIONS TABLE (NEW)
-- ============================================================
CREATE TABLE IF NOT EXISTS ai_report_versions (
    id SERIAL PRIMARY KEY,
    report_id INTEGER REFERENCES ai_financial_reports (id) ON DELETE CASCADE,
    version_number INTEGER NOT NULL,
    content TEXT NOT NULL,
    summary TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- 12. CREATE INDEXES FOR PERFORMANCE
-- ============================================================

-- User indexes
CREATE INDEX idx_users_email ON users (email);

CREATE INDEX idx_users_role ON users (role);

-- Document indexes
CREATE INDEX idx_documents_user_year ON documents (user_id, year);

CREATE INDEX idx_documents_category ON documents (category);

-- Financial records indexes
CREATE INDEX idx_financial_records_user_year ON financial_records (user_id, year);

-- AI Reports indexes
CREATE INDEX idx_ai_reports_user_year ON ai_financial_reports (user_id, year);

CREATE INDEX idx_ai_reports_status ON ai_financial_reports (status);

CREATE INDEX idx_ai_reports_accountant ON ai_financial_reports (accountant_id);

-- Accountant clients indexes
CREATE INDEX idx_accountant_clients_accountant ON accountant_clients (accountant_id);

CREATE INDEX idx_accountant_clients_client ON accountant_clients (client_id);

-- Commissions indexes
CREATE INDEX idx_commissions_accountant ON commissions (accountant_id);

CREATE INDEX idx_commissions_year ON commissions (year);

-- ============================================================
-- 13. SAMPLE DATA (Optional - for testing)
-- ============================================================

-- Insert sample users (OPTIONAL - comment out if not needed)
-- INSERT INTO users (email, password, role, first_name, last_name, business_name)
-- VALUES (
--     'doctor@example.com',
--     '$2y$10$...',  -- hashed password
--     'doctor',
--     'John',
--     'Doe',
--     'John Doe Medical Practice'
-- );

-- ============================================================
-- 14. VERIFY TABLES CREATED
-- ============================================================
-- Run this to verify all tables were created:
-- \dt

-- To see table structure:
-- \d table_name

-- ============================================================
-- END OF SETUP SCRIPT
-- ============================================================