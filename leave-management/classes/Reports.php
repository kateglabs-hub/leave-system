<?php
require_once __DIR__ . '/../config/database.php';

class Reports {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Individual employee report
    public function getIndividualReport($user_id, $year = null) {
        $year = $year ?: date('Y');
        
        // Get employee details
        $user_query = "SELECT u.*, d.name as department_name 
                      FROM users u 
                      LEFT JOIN departments d ON u.department_id = d.id 
                      WHERE u.id = :user_id";
        $user_stmt = $this->db->prepare($user_query);
        $user_stmt->bindParam(':user_id', $user_id);
        $user_stmt->execute();
        $user = $user_stmt->fetch();
        
        // Get leave balances
        $balance_query = "SELECT lb.*, lt.name as leave_type_name 
                         FROM leave_balances lb
                         JOIN leave_types lt ON lb.leave_type_id = lt.id
                         WHERE lb.user_id = :user_id AND lb.year = :year";
        $balance_stmt = $this->db->prepare($balance_query);
        $balance_stmt->bindParam(':user_id', $user_id);
        $balance_stmt->bindParam(':year', $year);
        $balance_stmt->execute();
        $balances = $balance_stmt->fetchAll();
        
        // Get leave history
        $history_query = "SELECT lr.*, lt.name as leave_type_name 
                         FROM leave_requests lr
                         JOIN leave_types lt ON lr.leave_type_id = lt.id
                         WHERE lr.user_id = :user_id 
                         AND YEAR(lr.start_date) = :year
                         ORDER BY lr.start_date DESC";
        $history_stmt = $this->db->prepare($history_query);
        $history_stmt->bindParam(':user_id', $user_id);
        $history_stmt->bindParam(':year', $year);
        $history_stmt->execute();
        $history = $history_stmt->fetchAll();
        
        // Get statistics
        $stats_query = "SELECT 
                          COUNT(*) as total_requests,
                          SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_count,
                          SUM(CASE WHEN status = 'approved' THEN total_days ELSE 0 END) as total_days_taken
                       FROM leave_requests
                       WHERE user_id = :user_id 
                       AND YEAR(start_date) = :year";
        $stats_stmt = $this->db->prepare($stats_query);
        $stats_stmt->bindParam(':user_id', $user_id);
        $stats_stmt->bindParam(':year', $year);
        $stats_stmt->execute();
        $stats = $stats_stmt->fetch();
        
        return [
            'user' => $user,
            'balances' => $balances,
            'history' => $history,
            'statistics' => $stats,
            'year' => $year
        ];
    }
    
    // Department report
    public function getDepartmentReport($department_id, $year = null) {
        $year = $year ?: date('Y');
        
        // Get department details
        $dept_query = "SELECT d.*, u.first_name, u.last_name 
                      FROM departments d
                      LEFT JOIN users u ON d.manager_id = u.id
                      WHERE d.id = :department_id";
        $dept_stmt = $this->db->prepare($dept_query);
        $dept_stmt->bindParam(':department_id', $department_id);
        $dept_stmt->execute();
        $department = $dept_stmt->fetch();
        
        // Get employee count
        $emp_query = "SELECT COUNT(*) as employee_count 
                     FROM users 
                     WHERE department_id = :department_id";
        $emp_stmt = $this->db->prepare($emp_query);
        $emp_stmt->bindParam(':department_id', $department_id);
        $emp_stmt->execute();
        $emp_count = $emp_stmt->fetch();
        
        // Get leave statistics by employee
        $employee_stats_query = "SELECT 
                                   u.id,
                                   u.employee_id,
                                   u.first_name,
                                   u.last_name,
                                   u.employee_level,
                                   COUNT(lr.id) as total_requests,
                                   SUM(CASE WHEN lr.status = 'approved' THEN lr.total_days ELSE 0 END) as days_taken,
                                   SUM(CASE WHEN lr.status = 'pending' THEN 1 ELSE 0 END) as pending_requests
                                FROM users u
                                LEFT JOIN leave_requests lr ON u.id = lr.user_id AND YEAR(lr.start_date) = :year
                                WHERE u.department_id = :department_id
                                GROUP BY u.id
                                ORDER BY u.last_name, u.first_name";
        $emp_stats_stmt = $this->db->prepare($employee_stats_query);
        $emp_stats_stmt->bindParam(':department_id', $department_id);
        $emp_stats_stmt->bindParam(':year', $year);
        $emp_stats_stmt->execute();
        $employee_stats = $emp_stats_stmt->fetchAll();
        
        // Get leave breakdown by type
        $type_breakdown_query = "SELECT 
                                   lt.name as leave_type,
                                   COUNT(lr.id) as request_count,
                                   SUM(CASE WHEN lr.status = 'approved' THEN lr.total_days ELSE 0 END) as total_days
                                FROM leave_types lt
                                LEFT JOIN leave_requests lr ON lt.id = lr.leave_type_id 
                                   AND YEAR(lr.start_date) = :year
                                   AND lr.user_id IN (SELECT id FROM users WHERE department_id = :department_id)
                                GROUP BY lt.id, lt.name
                                HAVING request_count > 0
                                ORDER BY total_days DESC";
        $type_stmt = $this->db->prepare($type_breakdown_query);
        $type_stmt->bindParam(':department_id', $department_id);
        $type_stmt->bindParam(':year', $year);
        $type_stmt->execute();
        $type_breakdown = $type_stmt->fetchAll();
        
        // Overall statistics
        $overall_query = "SELECT 
                           COUNT(lr.id) as total_requests,
                           SUM(CASE WHEN lr.status = 'approved' THEN lr.total_days ELSE 0 END) as total_days_taken,
                           SUM(CASE WHEN lr.status = 'pending' THEN 1 ELSE 0 END) as pending_requests
                         FROM leave_requests lr
                         JOIN users u ON lr.user_id = u.id
                         WHERE u.department_id = :department_id 
                         AND YEAR(lr.start_date) = :year";
        $overall_stmt = $this->db->prepare($overall_query);
        $overall_stmt->bindParam(':department_id', $department_id);
        $overall_stmt->bindParam(':year', $year);
        $overall_stmt->execute();
        $overall_stats = $overall_stmt->fetch();
        
        return [
            'department' => $department,
            'employee_count' => $emp_count['employee_count'],
            'employee_stats' => $employee_stats,
            'type_breakdown' => $type_breakdown,
            'overall_stats' => $overall_stats,
            'year' => $year
        ];
    }
    
    // Company-wide report
    public function getCompanyReport($year = null) {
        $year = $year ?: date('Y');
        
        // Total employees
        $emp_query = "SELECT COUNT(*) as total_employees FROM users WHERE role != 'admin'";
        $emp_stmt = $this->db->prepare($emp_query);
        $emp_stmt->execute();
        $emp_data = $emp_stmt->fetch();
        
        // Overall statistics
        $stats_query = "SELECT 
                         COUNT(lr.id) as total_requests,
                         SUM(CASE WHEN lr.status = 'approved' THEN 1 ELSE 0 END) as approved_requests,
                         SUM(CASE WHEN lr.status = 'pending' THEN 1 ELSE 0 END) as pending_requests,
                         SUM(CASE WHEN lr.status = 'rejected' THEN 1 ELSE 0 END) as rejected_requests,
                         SUM(CASE WHEN lr.status = 'approved' THEN lr.total_days ELSE 0 END) as total_days_taken,
                         AVG(CASE WHEN lr.status = 'approved' THEN lr.total_days ELSE NULL END) as avg_days_per_request
                       FROM leave_requests lr
                       WHERE YEAR(lr.start_date) = :year";
        $stats_stmt = $this->db->prepare($stats_query);
        $stats_stmt->bindParam(':year', $year);
        $stats_stmt->execute();
        $overall_stats = $stats_stmt->fetch();
        
        // Department breakdown
        $dept_query = "SELECT 
                         d.id,
                         d.name as department_name,
                         COUNT(DISTINCT u.id) as employee_count,
                         COUNT(lr.id) as total_requests,
                         SUM(CASE WHEN lr.status = 'approved' THEN lr.total_days ELSE 0 END) as days_taken
                       FROM departments d
                       LEFT JOIN users u ON d.id = u.department_id
                       LEFT JOIN leave_requests lr ON u.id = lr.user_id AND YEAR(lr.start_date) = :year
                       GROUP BY d.id, d.name
                       ORDER BY days_taken DESC";
        $dept_stmt = $this->db->prepare($dept_query);
        $dept_stmt->bindParam(':year', $year);
        $dept_stmt->execute();
        $dept_breakdown = $dept_stmt->fetchAll();
        
        // Leave type breakdown
        $type_query = "SELECT 
                         lt.name as leave_type,
                         COUNT(lr.id) as request_count,
                         SUM(CASE WHEN lr.status = 'approved' THEN lr.total_days ELSE 0 END) as total_days
                       FROM leave_types lt
                       LEFT JOIN leave_requests lr ON lt.id = lr.leave_type_id AND YEAR(lr.start_date) = :year
                       GROUP BY lt.id, lt.name
                       HAVING request_count > 0
                       ORDER BY total_days DESC";
        $type_stmt = $this->db->prepare($type_query);
        $type_stmt->bindParam(':year', $year);
        $type_stmt->execute();
        $type_breakdown = $type_stmt->fetchAll();
        
        // Monthly trend
        $monthly_query = "SELECT 
                           MONTH(start_date) as month,
                           MONTHNAME(start_date) as month_name,
                           COUNT(*) as request_count,
                           SUM(CASE WHEN status = 'approved' THEN total_days ELSE 0 END) as days_taken
                         FROM leave_requests
                         WHERE YEAR(start_date) = :year
                         GROUP BY MONTH(start_date), MONTHNAME(start_date)
                         ORDER BY MONTH(start_date)";
        $monthly_stmt = $this->db->prepare($monthly_query);
        $monthly_stmt->bindParam(':year', $year);
        $monthly_stmt->execute();
        $monthly_trend = $monthly_stmt->fetchAll();
        
        // Top leave takers
        $top_query = "SELECT 
                        u.employee_id,
                        u.first_name,
                        u.last_name,
                        d.name as department_name,
                        SUM(CASE WHEN lr.status = 'approved' THEN lr.total_days ELSE 0 END) as total_days_taken
                     FROM users u
                     LEFT JOIN departments d ON u.department_id = d.id
                     LEFT JOIN leave_requests lr ON u.id = lr.user_id AND YEAR(lr.start_date) = :year
                     GROUP BY u.id
                     HAVING total_days_taken > 0
                     ORDER BY total_days_taken DESC
                     LIMIT 10";
        $top_stmt = $this->db->prepare($top_query);
        $top_stmt->bindParam(':year', $year);
        $top_stmt->execute();
        $top_takers = $top_stmt->fetchAll();
        
        return [
            'total_employees' => $emp_data['total_employees'],
            'overall_stats' => $overall_stats,
            'department_breakdown' => $dept_breakdown,
            'type_breakdown' => $type_breakdown,
            'monthly_trend' => $monthly_trend,
            'top_takers' => $top_takers,
            'year' => $year
        ];
    }
    
    // Get all departments
    public function getAllDepartments() {
        $query = "SELECT d.*, u.first_name, u.last_name,
                  (SELECT COUNT(*) FROM users WHERE department_id = d.id) as employee_count
                  FROM departments d
                  LEFT JOIN users u ON d.manager_id = u.id
                  ORDER BY d.name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
