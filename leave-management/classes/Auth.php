<?php
require_once __DIR__ . '/../config/database.php';

class Auth {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    public function login($email, $password) {
        $query = "SELECT u.*, d.name as department_name 
                  FROM users u 
                  LEFT JOIN departments d ON u.department_id = d.id 
                  WHERE u.email = :email";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            
            if (password_verify($password, $user['password'])) {
                // Start session and store user data
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['employee_id'] = $user['employee_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['employee_level'] = $user['employee_level'];
                $_SESSION['department_id'] = $user['department_id'];
                $_SESSION['department_name'] = $user['department_name'];
                
                return ['success' => true, 'user' => $user];
            }
        }
        
        return ['success' => false, 'message' => 'Invalid email or password'];
    }
    
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        return ['success' => true];
    }
    
    public function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }
    
    public function getCurrentUser() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'employee_id' => $_SESSION['employee_id'],
            'email' => $_SESSION['email'],
            'full_name' => $_SESSION['full_name'],
            'role' => $_SESSION['role'],
            'employee_level' => $_SESSION['employee_level'],
            'department_id' => $_SESSION['department_id'],
            'department_name' => $_SESSION['department_name']
        ];
    }
    
    public function hasRole($roles) {
        $user = $this->getCurrentUser();
        if (!$user) return false;
        
        if (is_array($roles)) {
            return in_array($user['role'], $roles);
        }
        
        return $user['role'] === $roles;
    }
    
    public function register($data) {
        $query = "INSERT INTO users (employee_id, email, password, first_name, last_name, role, department_id, employee_level, hire_date) 
                  VALUES (:employee_id, :email, :password, :first_name, :last_name, :role, :department_id, :employee_level, :hire_date)";
        
        $stmt = $this->db->prepare($query);
        
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt->bindParam(':employee_id', $data['employee_id']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':role', $data['role']);
        $stmt->bindParam(':department_id', $data['department_id']);
        $stmt->bindParam(':employee_level', $data['employee_level']);
        $stmt->bindParam(':hire_date', $data['hire_date']);
        
        if ($stmt->execute()) {
            return ['success' => true, 'user_id' => $this->db->lastInsertId()];
        }
        
        return ['success' => false, 'message' => 'Registration failed'];
    }
}
