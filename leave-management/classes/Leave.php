<?php
require_once __DIR__ . '/../config/database.php';

class Leave {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Get all leave types
    public function getLeaveTypes() {
        $query = "SELECT * FROM leave_types WHERE is_active = 1 ORDER BY name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Get leave balance for a user
    public function getUserLeaveBalance($user_id, $year = null) {
        $year = $year ?: date('Y');
        
        $query = "SELECT lb.*, lt.name as leave_type_name, lt.is_paid 
                  FROM leave_balances lb
                  JOIN leave_types lt ON lb.leave_type_id = lt.id
                  WHERE lb.user_id = :user_id AND lb.year = :year
                  ORDER BY lt.name";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Initialize leave balance for a new user or new year
    public function initializeLeaveBalance($user_id, $year = null) {
        $year = $year ?: date('Y');
        
        // Get user's employee level
        $query = "SELECT employee_level FROM users WHERE id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $user = $stmt->fetch();
        
        if (!$user) return false;
        
        $employee_level = $user['employee_level'];
        
        // Get all active leave types
        $leave_types = $this->getLeaveTypes();
        
        foreach ($leave_types as $leave_type) {
            $days_allowed = ($employee_level === 'management') 
                ? $leave_type['days_allowed_management'] 
                : $leave_type['days_allowed_staff'];
            
            // Check if balance already exists
            $check_query = "SELECT id FROM leave_balances 
                           WHERE user_id = :user_id 
                           AND leave_type_id = :leave_type_id 
                           AND year = :year";
            $check_stmt = $this->db->prepare($check_query);
            $check_stmt->bindParam(':user_id', $user_id);
            $check_stmt->bindParam(':leave_type_id', $leave_type['id']);
            $check_stmt->bindParam(':year', $year);
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() === 0) {
                $insert_query = "INSERT INTO leave_balances 
                                (user_id, leave_type_id, year, total_days, used_days, remaining_days) 
                                VALUES (:user_id, :leave_type_id, :year, :total_days, 0, :remaining_days)";
                $insert_stmt = $this->db->prepare($insert_query);
                $insert_stmt->bindParam(':user_id', $user_id);
                $insert_stmt->bindParam(':leave_type_id', $leave_type['id']);
                $insert_stmt->bindParam(':year', $year);
                $insert_stmt->bindParam(':total_days', $days_allowed);
                $insert_stmt->bindParam(':remaining_days', $days_allowed);
                $insert_stmt->execute();
            }
        }
        
        return true;
    }
    
    // Create a leave request
    public function createLeaveRequest($data) {
        // Calculate total days (excluding weekends)
        $start = new DateTime($data['start_date']);
        $end = new DateTime($data['end_date']);
        $total_days = $this->calculateWorkingDays($start, $end);
        
        // Check if user has enough leave balance
        $query = "SELECT remaining_days FROM leave_balances 
                  WHERE user_id = :user_id 
                  AND leave_type_id = :leave_type_id 
                  AND year = :year";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':leave_type_id', $data['leave_type_id']);
        $year = date('Y');
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        
        $balance = $stmt->fetch();
        
        if (!$balance || $balance['remaining_days'] < $total_days) {
            return ['success' => false, 'message' => 'Insufficient leave balance'];
        }
        
        // Insert leave request
        $insert_query = "INSERT INTO leave_requests 
                        (user_id, leave_type_id, start_date, end_date, total_days, reason, documentation_path) 
                        VALUES (:user_id, :leave_type_id, :start_date, :end_date, :total_days, :reason, :documentation_path)";
        $insert_stmt = $this->db->prepare($insert_query);
        $insert_stmt->bindParam(':user_id', $data['user_id']);
        $insert_stmt->bindParam(':leave_type_id', $data['leave_type_id']);
        $insert_stmt->bindParam(':start_date', $data['start_date']);
        $insert_stmt->bindParam(':end_date', $data['end_date']);
        $insert_stmt->bindParam(':total_days', $total_days);
        $insert_stmt->bindParam(':reason', $data['reason']);
        $doc_path = $data['documentation_path'] ?? null;
        $insert_stmt->bindParam(':documentation_path', $doc_path);
        
        if ($insert_stmt->execute()) {
            return ['success' => true, 'request_id' => $this->db->lastInsertId(), 'total_days' => $total_days];
        }
        
        return ['success' => false, 'message' => 'Failed to create leave request'];
    }
    
    // Calculate working days between two dates
    private function calculateWorkingDays($start_date, $end_date) {
        $days = 0;
        $current = clone $start_date;
        
        while ($current <= $end_date) {
            $day_of_week = $current->format('N');
            if ($day_of_week < 6) { // Monday = 1, Friday = 5
                $days++;
            }
            $current->modify('+1 day');
        }
        
        return $days;
    }
    
    // Get leave requests for a user
    public function getUserLeaveRequests($user_id, $status = null) {
        $query = "SELECT lr.*, lt.name as leave_type_name, 
                  u.first_name, u.last_name,
                  approver.first_name as approver_first_name,
                  approver.last_name as approver_last_name
                  FROM leave_requests lr
                  JOIN leave_types lt ON lr.leave_type_id = lt.id
                  JOIN users u ON lr.user_id = u.id
                  LEFT JOIN users approver ON lr.approved_by = approver.id
                  WHERE lr.user_id = :user_id";
        
        if ($status) {
            $query .= " AND lr.status = :status";
        }
        
        $query .= " ORDER BY lr.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Get all leave requests (for HR/managers)
    public function getAllLeaveRequests($department_id = null, $status = null) {
        $query = "SELECT lr.*, lt.name as leave_type_name, 
                  u.first_name, u.last_name, u.employee_id, u.department_id,
                  d.name as department_name,
                  approver.first_name as approver_first_name,
                  approver.last_name as approver_last_name
                  FROM leave_requests lr
                  JOIN leave_types lt ON lr.leave_type_id = lt.id
                  JOIN users u ON lr.user_id = u.id
                  LEFT JOIN departments d ON u.department_id = d.id
                  LEFT JOIN users approver ON lr.approved_by = approver.id
                  WHERE 1=1";
        
        if ($department_id) {
            $query .= " AND u.department_id = :department_id";
        }
        
        if ($status) {
            $query .= " AND lr.status = :status";
        }
        
        $query .= " ORDER BY lr.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        
        if ($department_id) {
            $stmt->bindParam(':department_id', $department_id);
        }
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Approve or reject leave request
    public function updateLeaveRequestStatus($request_id, $status, $approved_by, $rejection_reason = null) {
        // Get the leave request details
        $query = "SELECT * FROM leave_requests WHERE id = :request_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':request_id', $request_id);
        $stmt->execute();
        $request = $stmt->fetch();
        
        if (!$request) {
            return ['success' => false, 'message' => 'Leave request not found'];
        }
        
        // Update leave request status
        $update_query = "UPDATE leave_requests 
                        SET status = :status, 
                            approved_by = :approved_by, 
                            approved_at = NOW(),
                            rejection_reason = :rejection_reason
                        WHERE id = :request_id";
        $update_stmt = $this->db->prepare($update_query);
        $update_stmt->bindParam(':status', $status);
        $update_stmt->bindParam(':approved_by', $approved_by);
        $update_stmt->bindParam(':rejection_reason', $rejection_reason);
        $update_stmt->bindParam(':request_id', $request_id);
        
        if ($update_stmt->execute()) {
            // If approved, update leave balance
            if ($status === 'approved') {
                $year = date('Y', strtotime($request['start_date']));
                $balance_query = "UPDATE leave_balances 
                                 SET used_days = used_days + :total_days,
                                     remaining_days = remaining_days - :total_days
                                 WHERE user_id = :user_id 
                                 AND leave_type_id = :leave_type_id 
                                 AND year = :year";
                $balance_stmt = $this->db->prepare($balance_query);
                $balance_stmt->bindParam(':total_days', $request['total_days']);
                $balance_stmt->bindParam(':user_id', $request['user_id']);
                $balance_stmt->bindParam(':leave_type_id', $request['leave_type_id']);
                $balance_stmt->bindParam(':year', $year);
                $balance_stmt->execute();
            }
            
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => 'Failed to update leave request'];
    }
    
    // Get leave statistics for dashboard
    public function getLeaveStatistics($user_id = null, $department_id = null, $year = null) {
        $year = $year ?: date('Y');
        
        $where_clauses = ["YEAR(lr.start_date) = :year"];
        
        if ($user_id) {
            $where_clauses[] = "lr.user_id = :user_id";
        }
        
        if ($department_id) {
            $where_clauses[] = "u.department_id = :department_id";
        }
        
        $where = implode(' AND ', $where_clauses);
        
        $query = "SELECT 
                    COUNT(*) as total_requests,
                    SUM(CASE WHEN lr.status = 'pending' THEN 1 ELSE 0 END) as pending_requests,
                    SUM(CASE WHEN lr.status = 'approved' THEN 1 ELSE 0 END) as approved_requests,
                    SUM(CASE WHEN lr.status = 'rejected' THEN 1 ELSE 0 END) as rejected_requests,
                    SUM(CASE WHEN lr.status = 'approved' THEN lr.total_days ELSE 0 END) as total_days_taken
                  FROM leave_requests lr
                  JOIN users u ON lr.user_id = u.id
                  WHERE $where";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':year', $year);
        
        if ($user_id) {
            $stmt->bindParam(':user_id', $user_id);
        }
        if ($department_id) {
            $stmt->bindParam(':department_id', $department_id);
        }
        
        $stmt->execute();
        return $stmt->fetch();
    }
    
    // Get leave breakdown by type
    public function getLeaveBreakdownByType($user_id = null, $department_id = null, $year = null) {
        $year = $year ?: date('Y');
        
        $where_clauses = ["YEAR(lr.start_date) = :year", "lr.status = 'approved'"];
        
        if ($user_id) {
            $where_clauses[] = "lr.user_id = :user_id";
        }
        
        if ($department_id) {
            $where_clauses[] = "u.department_id = :department_id";
        }
        
        $where = implode(' AND ', $where_clauses);
        
        $query = "SELECT 
                    lt.name as leave_type,
                    COUNT(*) as request_count,
                    SUM(lr.total_days) as total_days
                  FROM leave_requests lr
                  JOIN leave_types lt ON lr.leave_type_id = lt.id
                  JOIN users u ON lr.user_id = u.id
                  WHERE $where
                  GROUP BY lt.id, lt.name
                  ORDER BY total_days DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':year', $year);
        
        if ($user_id) {
            $stmt->bindParam(':user_id', $user_id);
        }
        if ($department_id) {
            $stmt->bindParam(':department_id', $department_id);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
