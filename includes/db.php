<?php
// Database connection using environment variables (Replit PostgreSQL)
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo !== null) {
        return $pdo;
    }
    
    $host     = getenv('PGHOST')     ?: 'localhost';
    $port     = getenv('PGPORT')     ?: '5432';
    $dbname   = getenv('PGDATABASE') ?: 'meditax';
    $user     = getenv('PGUSER')     ?: 'postgres';
    $password = getenv('PGPASSWORD') ?: '';
    
    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
    
    try {
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        die("Database connection error. Please try again later.");
    }
}

// Initialize database schema (creates tables if they don't exist)
function initializeDatabase() {
    $pdo = getDBConnection();
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(50) NOT NULL,
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
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS subscriptions (
            id SERIAL PRIMARY KEY,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            status VARCHAR(50) DEFAULT 'inactive',
            plan_name VARCHAR(100) DEFAULT 'Professional',
            amount DECIMAL(10, 2) DEFAULT 80.00,
            start_date DATE,
            end_date DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS accountant_clients (
            id SERIAL PRIMARY KEY,
            accountant_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            client_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            status VARCHAR(50) DEFAULT 'pending',
            year INTEGER NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS documents (
            id SERIAL PRIMARY KEY,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            filename VARCHAR(255) NOT NULL,
            original_name VARCHAR(255) NOT NULL,
            file_type VARCHAR(50) NOT NULL,
            file_size INTEGER NOT NULL,
            category VARCHAR(100),
            year INTEGER NOT NULL,
            description TEXT,
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS financial_records (
            id SERIAL PRIMARY KEY,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            year INTEGER NOT NULL,
            total_income DECIMAL(15, 2) DEFAULT 0,
            total_expenses DECIMAL(15, 2) DEFAULT 0,
            estimated_profit DECIMAL(15, 2) DEFAULT 0,
            estimated_tax DECIMAL(15, 2) DEFAULT 0,
            tax_rate DECIMAL(5, 2) DEFAULT 25.00,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE(user_id, year)
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS commissions (
            id SERIAL PRIMARY KEY,
            accountant_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            client_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            amount DECIMAL(10, 2) NOT NULL,
            commission_rate DECIMAL(5, 2) DEFAULT 12.00,
            commission_amount DECIMAL(10, 2) NOT NULL,
            year INTEGER NOT NULL,
            status VARCHAR(50) DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tax_reports (
            id SERIAL PRIMARY KEY,
            client_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            accountant_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            year INTEGER NOT NULL,
            status VARCHAR(50) DEFAULT 'draft',
            total_income DECIMAL(15, 2),
            total_deductions DECIMAL(15, 2),
            taxable_income DECIMAL(15, 2),
            tax_liability DECIMAL(15, 2),
            summary TEXT,
            report_file VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS messages (
            id SERIAL PRIMARY KEY,
            sender_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            receiver_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            subject VARCHAR(255),
            message TEXT NOT NULL,
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS ai_financial_reports (
            id SERIAL PRIMARY KEY,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            accountant_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
            year INTEGER NOT NULL,
            report_type VARCHAR(100) NOT NULL,
            title VARCHAR(255) NOT NULL,
            summary TEXT NOT NULL,
            detailed_analysis TEXT,
            recommendations TEXT,
            key_metrics TEXT,
            charts_data TEXT,
            status VARCHAR(50) DEFAULT 'draft',
            ai_model VARCHAR(100),
            generation_method VARCHAR(50),
            created_by INTEGER REFERENCES users(id) ON DELETE SET NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS ai_report_versions (
            id SERIAL PRIMARY KEY,
            report_id INTEGER NOT NULL REFERENCES ai_financial_reports(id) ON DELETE CASCADE,
            version_number INTEGER NOT NULL,
            content TEXT NOT NULL,
            summary TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
}
?>
