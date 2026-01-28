-- Initialize default users with correct password hash for admin123
-- Hash: password_hash('admin123', PASSWORD_DEFAULT)

-- Delete existing users if they have placeholder password
DELETE FROM users WHERE email IN ('admin@company.com', 'hr@company.com');

-- Insert default admin user (password: admin123)
INSERT INTO users (employee_id, email, password, first_name, last_name, role, department_id, employee_level, hire_date) 
VALUES ('EMP001', 'admin@company.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm', 'Admin', 'User', 'admin', 1, 'management', '2020-01-01');

-- Insert default HR user (password: admin123)
INSERT INTO users (employee_id, email, password, first_name, last_name, role, department_id, employee_level, hire_date) 
VALUES ('EMP002', 'hr@company.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm', 'HR', 'Manager', 'hr', 1, 'management', '2020-01-01');

-- Initialize leave balances for admin user
INSERT INTO leave_balances (user_id, leave_type_id, year, total_days, used_days, remaining_days)
SELECT 1, id, YEAR(NOW()), 
    CASE id 
        WHEN 1 THEN 25
        WHEN 2 THEN 15
        WHEN 3 THEN 7
        WHEN 4 THEN 90
        WHEN 5 THEN 14
        WHEN 6 THEN 10
        WHEN 7 THEN 0
    END,
    0,
    CASE id 
        WHEN 1 THEN 25
        WHEN 2 THEN 15
        WHEN 3 THEN 7
        WHEN 4 THEN 90
        WHEN 5 THEN 14
        WHEN 6 THEN 10
        WHEN 7 THEN 0
    END
FROM leave_types
WHERE NOT EXISTS (SELECT 1 FROM leave_balances WHERE user_id = 1 AND year = YEAR(NOW()));
