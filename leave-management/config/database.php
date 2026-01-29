<?php
class Database {
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    private $conn;
    private $usePooling;

    public function __construct() {
        // Support Neon DATABASE_URL format: postgresql://user:password@host/dbname
        $databaseUrl = getenv('DATABASE_URL');
        
        if ($databaseUrl) {
            // Parse Neon connection string
            $this->parseNeonUrl($databaseUrl);
        } else {
            // Fallback to individual environment variables (local development)
            $this->host = getenv('DB_HOST') ?: 'localhost';
            $this->port = getenv('DB_PORT') ?: '5432';
            $this->db_name = getenv('DB_NAME') ?: 'leave_management';
            $this->username = getenv('DB_USER') ?: 'postgres';
            $this->password = getenv('DB_PASSWORD') ?: '';
            $this->usePooling = false;
        }
    }

    private function parseNeonUrl($url) {
        // Parse: postgresql://user:password@host/dbname?sslmode=require
        $parts = parse_url($url);
        
        $this->username = $parts['user'] ?? 'postgres';
        $this->password = $parts['pass'] ?? '';
        $this->host = $parts['host'] ?? 'localhost';
        $this->port = $parts['port'] ?? 5432;
        $this->db_name = ltrim($parts['path'] ?? '/leave_management', '/');
        $this->usePooling = true; // Always use pooling with Neon
    }

    public function getConnection() {
        $this->conn = null;

        try {
            // Build DSN with SSL support for Neon
            $dsn = "pgsql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name;
            
            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 5,
                // Connection pooling settings for Neon
                PDO::ATTR_PERSISTENT => false // Don't use persistent connections with pooling
            );
            
            // Add SSL if in production (Neon requires it)
            if ($this->usePooling || getenv('APP_ENV') === 'production') {
                $dsn .= ";sslmode=require";
            }
            
            $this->conn = new PDO(
                $dsn,
                $this->username,
                $this->password,
                $options
            );
            
            // Set connection timeout
            $this->conn->setAttribute(PDO::ATTR_TIMEOUT, 5);
            
        } catch(PDOException $e) {
            error_log("Connection error: " . $e->getMessage());
            throw new Exception("Database connection failed: " . $e->getMessage());
        }

        return $this->conn;
    }

    public function closeConnection() {
        $this->conn = null;
    }
}
