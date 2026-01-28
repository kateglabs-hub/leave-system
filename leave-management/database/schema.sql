-- Leave Management System Database Schema

-- Users/Employees Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role ENUM('employee', 'manager', 'hr', 'admin') DEFAULT 'employee',
    department_id INT,
    employee_level ENUM('staff', 'management') DEFAULT 'staff',
    hire_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_department (department_id),
    INDEX idx_role (role)
);

-- Departments Table
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    manager_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Leave Types Table
CREATE TABLE IF NOT EXISTS leave_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    days_allowed_staff INT NOT NULL DEFAULT 0,
    days_allowed_management INT NOT NULL DEFAULT 0,
    requires_documentation BOOLEAN DEFAULT FALSE,
    is_paid BOOLEAN DEFAULT TRUE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Leave Balances Table
CREATE TABLE IF NOT EXISTS leave_balances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    leave_type_id INT NOT NULL,
    year INT NOT NULL,
    total_days DECIMAL(5,1) NOT NULL,
    used_days DECIMAL(5,1) DEFAULT 0,
    remaining_days DECIMAL(5,1) NOT NULL,
    carried_forward DECIMAL(5,1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_leave_year (user_id, leave_type_id, year),
    INDEX idx_user_year (user_id, year)
);

-- Leave Requests Table
CREATE TABLE IF NOT EXISTS leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    leave_type_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_days DECIMAL(5,1) NOT NULL,
    reason TEXT,
    status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
    approved_by INT,
    approved_at TIMESTAMP NULL,
    rejection_reason TEXT,
    documentation_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id),
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_status (user_id, status),
    INDEX idx_dates (start_date, end_date),
    INDEX idx_status (status)
);

-- Add foreign key for departments
ALTER TABLE users
ADD FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL;

-- Insert default leave types
INSERT INTO leave_types (name, description, days_allowed_staff, days_allowed_management, requires_documentation, is_paid) VALUES
('Annual Leave', 'Regular annual vacation leave', 21, 25, FALSE, TRUE),
('Sick Leave', 'Medical leave for illness', 10, 15, TRUE, TRUE),
('Compassionate Leave', 'Leave for family emergencies or bereavement', 5, 7, TRUE, TRUE),
('Maternity Leave', 'Maternity leave for mothers', 90, 90, TRUE, TRUE),
('Paternity Leave', 'Paternity leave for fathers', 10, 14, TRUE, TRUE),
('Study Leave', 'Leave for educational purposes', 5, 10, TRUE, FALSE),
('Unpaid Leave', 'Leave without pay', 0, 0, TRUE, FALSE);

-- Insert default departments
INSERT INTO departments (name, description) VALUES
('Human Resources', 'HR Department'),
('Information Technology', 'IT Department'),
('Finance', 'Finance Department'),
('Marketing', 'Marketing Department'),
('Operations', 'Operations Department'),
('Sales', 'Sales Department');

-- Insert default admin user (password: admin123 - hashed with bcrypt)
INSERT INTO users (employee_id, email, password, first_name, last_name, role, department_id, employee_level, hire_date) VALUES
('EMP001', 'admin@company.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm', 'Admin', 'User', 'admin', 1, 'management', '2020-01-01'),
('EMP002', 'hr@company.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm', 'HR', 'Manager', 'hr', 1, 'management', '2020-01-01');
