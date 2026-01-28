<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Leave.php';
require_once __DIR__ . '/../classes/Reports.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get request method and endpoint
$method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path_parts = explode('/', trim($path, '/'));

// Get the endpoint (last part of URL)
$endpoint = end($path_parts);

// Get request body for POST/PUT requests
$input = json_decode(file_get_contents('php://input'), true);

// Initialize classes
$auth = new Auth();
$leave = new Leave();
$reports = new Reports();

try {
    // Authentication endpoints
    if ($endpoint === 'login' && $method === 'POST') {
        $result = $auth->login($input['email'], $input['password']);
        echo json_encode($result);
        exit();
    }
    
    if ($endpoint === 'logout' && $method === 'POST') {
        $result = $auth->logout();
        echo json_encode($result);
        exit();
    }
    
    if ($endpoint === 'register' && $method === 'POST') {
        // Only HR and Admin can register new users
        if (!$auth->hasRole(['hr', 'admin'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        $result = $auth->register($input);
        echo json_encode($result);
        exit();
    }
    
    if ($endpoint === 'current-user' && $method === 'GET') {
        $user = $auth->getCurrentUser();
        if ($user) {
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        }
        exit();
    }
    
    // Check authentication for protected endpoints
    if (!$auth->isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit();
    }
    
    $current_user = $auth->getCurrentUser();
    
    // Leave type endpoints
    if ($endpoint === 'leave-types' && $method === 'GET') {
        $types = $leave->getLeaveTypes();
        echo json_encode(['success' => true, 'leave_types' => $types]);
        exit();
    }
    
    // Leave balance endpoints
    if ($endpoint === 'leave-balance' && $method === 'GET') {
        $user_id = $_GET['user_id'] ?? $current_user['id'];
        $year = $_GET['year'] ?? date('Y');
        
        // Users can only view their own balance unless they're HR/admin
        if ($user_id != $current_user['id'] && !$auth->hasRole(['hr', 'admin'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        
        $balance = $leave->getUserLeaveBalance($user_id, $year);
        echo json_encode(['success' => true, 'balance' => $balance]);
        exit();
    }
    
    if ($endpoint === 'initialize-balance' && $method === 'POST') {
        $user_id = $input['user_id'];
        $year = $input['year'] ?? date('Y');
        
        if (!$auth->hasRole(['hr', 'admin'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        
        $result = $leave->initializeLeaveBalance($user_id, $year);
        echo json_encode(['success' => $result]);
        exit();
    }
    
    // Leave request endpoints
    if ($endpoint === 'leave-requests' && $method === 'GET') {
        if (isset($_GET['user_id'])) {
            $user_id = $_GET['user_id'];
            $status = $_GET['status'] ?? null;
            
            // Users can only view their own requests unless they're HR/admin
            if ($user_id != $current_user['id'] && !$auth->hasRole(['hr', 'admin', 'manager'])) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }
            
            $requests = $leave->getUserLeaveRequests($user_id, $status);
        } else {
            // Get all requests (for HR/managers)
            if (!$auth->hasRole(['hr', 'admin', 'manager'])) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }
            
            $department_id = $auth->hasRole('manager') ? $current_user['department_id'] : ($_GET['department_id'] ?? null);
            $status = $_GET['status'] ?? null;
            
            $requests = $leave->getAllLeaveRequests($department_id, $status);
        }
        
        echo json_encode(['success' => true, 'requests' => $requests]);
        exit();
    }
    
    if ($endpoint === 'leave-request' && $method === 'POST') {
        $input['user_id'] = $current_user['id'];
        $result = $leave->createLeaveRequest($input);
        echo json_encode($result);
        exit();
    }
    
    if ($endpoint === 'leave-request-status' && $method === 'PUT') {
        if (!$auth->hasRole(['hr', 'admin', 'manager'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        
        $result = $leave->updateLeaveRequestStatus(
            $input['request_id'],
            $input['status'],
            $current_user['id'],
            $input['rejection_reason'] ?? null
        );
        echo json_encode($result);
        exit();
    }
    
    // Statistics endpoints
    if ($endpoint === 'leave-statistics' && $method === 'GET') {
        $user_id = $_GET['user_id'] ?? ($auth->hasRole(['hr', 'admin']) ? null : $current_user['id']);
        $department_id = $_GET['department_id'] ?? null;
        $year = $_GET['year'] ?? date('Y');
        
        $stats = $leave->getLeaveStatistics($user_id, $department_id, $year);
        echo json_encode(['success' => true, 'statistics' => $stats]);
        exit();
    }
    
    if ($endpoint === 'leave-breakdown' && $method === 'GET') {
        $user_id = $_GET['user_id'] ?? ($auth->hasRole(['hr', 'admin']) ? null : $current_user['id']);
        $department_id = $_GET['department_id'] ?? null;
        $year = $_GET['year'] ?? date('Y');
        
        $breakdown = $leave->getLeaveBreakdownByType($user_id, $department_id, $year);
        echo json_encode(['success' => true, 'breakdown' => $breakdown]);
        exit();
    }
    
    // Report endpoints
    if ($endpoint === 'report-individual' && $method === 'GET') {
        $user_id = $_GET['user_id'] ?? $current_user['id'];
        $year = $_GET['year'] ?? date('Y');
        
        // Users can only view their own report unless they're HR/admin
        if ($user_id != $current_user['id'] && !$auth->hasRole(['hr', 'admin'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        
        $report = $reports->getIndividualReport($user_id, $year);
        echo json_encode(['success' => true, 'report' => $report]);
        exit();
    }
    
    if ($endpoint === 'report-department' && $method === 'GET') {
        if (!$auth->hasRole(['hr', 'admin', 'manager'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        
        $department_id = $_GET['department_id'] ?? $current_user['department_id'];
        $year = $_GET['year'] ?? date('Y');
        
        $report = $reports->getDepartmentReport($department_id, $year);
        echo json_encode(['success' => true, 'report' => $report]);
        exit();
    }
    
    if ($endpoint === 'report-company' && $method === 'GET') {
        if (!$auth->hasRole(['hr', 'admin'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        
        $year = $_GET['year'] ?? date('Y');
        
        $report = $reports->getCompanyReport($year);
        echo json_encode(['success' => true, 'report' => $report]);
        exit();
    }
    
    if ($endpoint === 'departments' && $method === 'GET') {
        $departments = $reports->getAllDepartments();
        echo json_encode(['success' => true, 'departments' => $departments]);
        exit();
    }
    
    // If no endpoint matched
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
