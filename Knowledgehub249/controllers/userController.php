
<?php
require_once 'DBController.php';
require_once 'SessionController.php';



class UserController {
    private $db;
    private $sessionController;
    
    public function __construct($db) {
        $this->db = $db;
        // Ensure the database connection is open
        if (!$this->db->connection) {
            $this->db->openConnection();
        }
        $this->sessionController = new SessionController();
    }
    
    public function register($username, $email, $password) {
        // Check if username already exists
        $username = $this->db->connection->real_escape_string($username);
        $email = $this->db->connection->real_escape_string($email);
        
        $query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $result = $this->db->select($query);
        
        if (!empty($result)) {
            return [
                'success' => false,
                'message' => 'اسم المستخدم أو البريد الإلكتروني مستخدم بالفعل'
            ];
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $query = "INSERT INTO users (username, email, password, reputation, created_at) 
                  VALUES ('$username', '$email', '$hashedPassword', 0, NOW())";
        $userId = $this->db->insert($query);
        
        if ($userId) {
            // Log user in
            $this->sessionController->login($userId, $username);
            
            return [
                'success' => true,
                'message' => 'تم التسجيل بنجاح!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'حدث خطأ أثناء التسجيل'
            ];
        }
    }
    
    public function login($email, $password) {
        $email = $this->db->connection->real_escape_string($email);
        
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = $this->db->select($query);
        
        if (empty($result)) {
            return [
                'success' => false,
                'message' => 'البريد الإلكتروني غير موجود'
            ];
        }
        
        $user = $result[0];
        
        if (password_verify($password, $user['password'])) {
            // Pass the correct is_admin value
            $isAdmin = isset($user['is_admin']) ? $user['is_admin'] : 0;
            $this->sessionController->login($user['id'], $user['username'], $isAdmin);
            
            return [
                'success' => true,
                'message' => 'تم تسجيل الدخول بنجاح!',
                'isAdmin' => $isAdmin == 1
            ];
        } else {
            return [
                'success' => false,
                'message' => 'كلمة المرور غير صحيحة'
            ];
        }
    }
    
    public function getUserById($userId) {
        if (!$userId) {
            return null;
        }
        
        $userId = (int)$userId;
        $query = "SELECT * FROM users WHERE id = $userId";
        $result = $this->db->select($query);
        
        if (!empty($result)) {
            return $result[0];
        }
        
        return null;
    }
    
    public function getUserObject($userId) {
        $userData = $this->getUserById($userId);
        if ($userData) {
            return new User($userData);
        }
        return null;
    }
    public function processProfileUpdate() {
        $response = ['success' => false, 'message' => 'فشلت المعالجة الأولية.']; // Default response
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response['message'] = 'طريقة الطلب غير صالحة.';
            return $response;
        }
    
        if (!isset($_SESSION['user_id'])) {
            $response['message'] = 'المستخدم غير مصادق عليه. يرجى تسجيل الدخول.';
            return $response;
        }
    
        $inputJSON = file_get_contents('php://input');
        $inputData = json_decode($inputJSON, true);
    
        if ($inputData === null) {
            $response['message'] = 'بيانات الإدخال المستلمة غير صالحة أو فارغة.';
            return $response;
        }
    
        $userId = $_SESSION['user_id'];
        $username = $inputData['username'] ?? null;
        $email = $inputData['email'] ?? null;
        $password = $inputData['password'] ?? null; // Optional
        $language = $inputData['language'] ?? null;
    
        // --- Basic Validation ---
        if (empty($username) || empty($email) || empty($language)) {
            $response['message'] = 'البيانات المطلوبة مفقودة (اسم المستخدم، البريد الإلكتروني، اللغة).';
            return $response;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] = 'صيغة البريد الإلكتروني المقدمة غير صحيحة.';
            return $response;
        }
    
        // --- Sanitize Data ---
        $username = htmlspecialchars(trim($username), ENT_QUOTES, 'UTF-8');
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        // Language usually doesn't need heavy sanitization if it's from a fixed set ('ar', 'en')
    
        $updateDataForDb = [
            'username' => $username,
            'email' => $email,
        ];
    
        // IMPORTANT: Add 'language' to $updateDataForDb ONLY if 'language' is a column in your 'users' table.
        // Example: if ($this->isLanguageStoredInDb()) { $updateDataForDb['language'] = $language; }
        // For now, assuming language is primarily a session preference unless explicitly a DB field.
    
    
        if (!empty($password)) {
            // Password was already confirmed client-side by your JavaScript
            $updateDataForDb['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
    
        // Call the existing updateProfile method (which interacts with the DB)
        // We only attempt DB update if there are fields to update for the DB.
        $dbUpdateAttempted = !empty($updateDataForDb); // True if username, email, or password changed
        $dbUpdateResult = false;
    
        if ($dbUpdateAttempted) {
            $dbUpdateResult = $this->updateProfile($userId, $updateDataForDb);
        }
    
        // Determine overall success
        if ($dbUpdateAttempted && $dbUpdateResult) {
            // DB update was successful
            $_SESSION['username'] = $username; // Update session username
            $_SESSION['language'] = $language; // Always update session language preference
    
            $_SESSION['profile_message'] = 'تم تحديث الملف الشخصي بنجاح!';
            $_SESSION['profile_success'] = true;
            $response = ['success' => true, 'message' => 'تم تحديث الملف الشخصي بنجاح!'];
    
        } else if ($dbUpdateAttempted && !$dbUpdateResult) {
            // DB update was attempted but failed
            $_SESSION['profile_message'] = 'فشل تحديث الملف الشخصي في قاعدة البيانات. قد تكون البيانات مكررة.';
            $_SESSION['profile_success'] = false;
            $response['message'] = 'فشل تحديث الملف الشخصي في قاعدة البيانات. قد تكون البيانات مكررة.';
        } else if (!$dbUpdateAttempted && isset($inputData['language']) && $_SESSION['language'] !== $language) {
            // No DB fields changed, but language preference (session) did.
            $_SESSION['language'] = $language;
            $_SESSION['profile_message'] = 'تم تحديث تفضيل اللغة بنجاح.';
            $_SESSION['profile_success'] = true;
            $response = ['success' => true, 'message' => 'تم تحديث تفضيل اللغة بنجاح.'];
        } else {
            // No actual changes or an unhandled scenario
            $_SESSION['profile_message'] = 'لم يتم إجراء أي تغييرات أو فشل التحديث بشكل غير متوقع.';
            $_SESSION['profile_success'] = false;
            $response['message'] = 'لم يتم إجراء أي تغييرات أو فشل التحديث بشكل غير متوقع.';
        }
    
        return $response;
    }
    
    public function updateProfile($userId, $data) {
        if (!$userId) {
            return false;
        }
        
        $userId = (int)$userId;
        $fields = [];
        
        foreach ($data as $key => $value) {
            if ($key != 'id') {
                $value = $this->db->connection->real_escape_string($value);
                $fields[] = "$key = '$value'";
            }
        }
        
        $query = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = $userId";
        $result = $this->db->update($query);
        
        return $result ? true : false;
    }
    
    public function getUserReputation($userId) {
        if (!$userId) {
            return 0;
        }
        
        $userId = (int)$userId;
        $query = "SELECT reputation FROM users WHERE id = $userId";
        $result = $this->db->select($query);
        
        if (!empty($result)) {
            return $result[0]['reputation'];
        }
        
        return 0;
    }
    
    public function getUserBadge($userId) {
        $reputation = $this->getUserReputation($userId);
        
        if ($reputation >= Badge::GOLD_THRESHOLD) {
            return [
                'type' => Badge::GOLD,
                'name' => 'ذهبي',
                'icon' => 'fas fa-medal text-warning'
            ];
        } else if ($reputation >= Badge::BRONZE_THRESHOLD) {
            return [
                'type' => Badge::BRONZE,
                'name' => 'برونزي',
                'icon' => 'fas fa-medal text-danger'
            ];
        } else if ($reputation >= Badge::SILVER_THRESHOLD) {
            return [
                'type' => Badge::SILVER,
                'name' => 'فضي',
                'icon' => 'fas fa-medal text-secondary'
            ];
        } else {
            return [
                'type' => 'beginner',
                'name' => 'مبتدئ',
                'icon' => 'fas fa-user text-info'
            ];
        }
    }
    
    public function canUserEditPosts($userId) {
        $user = $this->getUserObject($userId);
        if (!$user) {
            return false;
        }
        return $user->canEditPosts();
    }
}
?>
