
<?php
require_once '../../controllers/SessionController.php';

class AdminController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    // Display admin dashboard
    public function dashboard() {
        // Check if user is admin before showing dashboard
        SessionController::requireAdmin();
        
        // Get statistics for admin dashboard
        $query = "SELECT 
            (SELECT COUNT(*) FROM users) as total_users,
            (SELECT COUNT(*) FROM questions) as total_questions,
            (SELECT COUNT(*) FROM answers) as total_answers,
            (SELECT COUNT(*) FROM reports) as total_reports";
        
        $stats = $this->db->select($query);
        
        return $stats ? $stats[0] : [
            'total_users' => 0,
            'total_questions' => 0, 
            'total_answers' => 0,
            'total_reports' => 0
        ];
    }
}
?>