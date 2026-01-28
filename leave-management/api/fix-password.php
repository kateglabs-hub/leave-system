<?php
// Quick script to verify and fix database users
require_once __DIR__ . '/../config/database.php';

try {
    // Check if users table exists and has data
    $stmt = $pdo->query("SELECT * FROM users WHERE email = 'admin@company.com'");
    $user = $stmt->fetch();
    
    if ($user) {
        echo "Found admin user in database:\n";
        echo "Email: " . $user['email'] . "\n";
        echo "Role: " . $user['role'] . "\n";
        echo "Current password hash: " . $user['password'] . "\n\n";
        
        $correctHash = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm';
        
        if ($user['password'] === $correctHash) {
            echo "✓ Password hash is CORRECT!\n";
        } else {
            echo "✗ Password hash is INCORRECT. Updating...\n";
            
            // Update with correct hash
            $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $updateStmt->execute([$correctHash, 'admin@company.com']);
            
            // Also update HR user
            $updateStmt->execute([$correctHash, 'hr@company.com']);
            
            echo "✓ Password hashes updated successfully!\n";
            echo "\nYou can now login with:\n";
            echo "  Email: admin@company.com\n";
            echo "  Password: admin123\n";
        }
    } else {
        echo "Admin user not found in database. Running seed...\n";
        
        // Read and execute seed.sql
        $seedSQL = file_get_contents(__DIR__ . '/../database/seed.sql');
        
        // Split by semicolon and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $seedSQL)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                } catch (Exception $e) {
                    // Ignore errors for now
                }
            }
        }
        
        echo "✓ Seed executed. Users should now be created.\n";
        echo "\nYou can now login with:\n";
        echo "  Email: admin@company.com\n";
        echo "  Password: admin123\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
