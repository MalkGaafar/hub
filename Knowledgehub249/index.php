
<?php
session_start();

// Include necessary controllers
require_once 'views/assets/fpdf/fpdf.php';
require_once 'controllers/DBController.php';
require_once 'controllers/userController.php';
require_once 'controllers/QuestionController.php';
require_once 'controllers/AnswerController.php';
require_once 'controllers/TagController.php';
require_once 'controllers/SessionController.php';
require_once 'controllers/NotificationController.php';
require_once 'controllers/ReportController.php';
require_once 'controllers/SpammedPostController.php';
require_once 'controllers/GroupController.php';
require_once 'controllers/VoteController.php';


// Create uploads directory if it doesn't exist
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

// Create reports directory if it doesn't exist
if (!file_exists('reports')) {
    mkdir('reports', 0777, true);
}

// Initialize database connection
$db = new DBController();
$db->openConnection();

// Initialize controllers
$userController = new UserController($db);
$questionController = new QuestionController($db);
$sessionController = new SessionController();
$answerController = new AnswerController($db);
$voteController = new VoteController($db);
$tagController = new TagController($db);
$notificationController = new NotificationController($db);
$reportController = new ReportController($db);
$spammedPostController = new SpammedPostController($db);
$groupController = new GroupController($db);

// Simple routing logic
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Check if user is logged in
$isLoggedIn = $sessionController->isLoggedIn();
$isAdmin = $isLoggedIn && $sessionController->isAdmin();

// Language switching
if ($page == 'switch-language' && isset($_GET['lang'])) {
    $lang = $_GET['lang'] === 'en' ? 'en' : 'ar';
    $sessionController->setLanguage($lang);
    
    // Redirect back to the previous page or home
    $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
    header('Location: ' . $redirect);
    exit;
}

// Include header
include 'views/layouts/Head.php';

// Route to appropriate page
switch ($page) {
    case 'home':
        $questions = $questionController->getRecentQuestions();
        include 'views/User/questions/indexQues.php';
        break;
        
        case 'register':
            if ($isLoggedIn) {
                header('Location: index.php');
                exit;
            }
            include 'views/User/register.php';  // Changed from users/register.php to Auth/register.php
            break;
        
            case 'login':
                if ($isLoggedIn) {
                    header('Location: index.php');
                    exit;
                }
                include 'views/Auth/login.php';  // Changed from users/login.php to Auth/login.php
                break;
    case 'logout':
        $sessionController->logout();
        header('Location: index.php');
        exit;
        break;
        
    case 'profile':
        if (!$isLoggedIn) {
            header('Location: index.php?page=login');
            exit;
        }
        $user = $userController->getUserById($_SESSION['user_id']);
        $userQuestions = $questionController->getQuestionsByUser($_SESSION['user_id']);
        include 'views/User/profile.php';
        break;
    case 'edit-profile':
            if (!isset($_SESSION['user_id'])) {
                header('Location: index.php?page=login');
                exit;
            }
            $user=$userController->processProfileUpdate($_SESSION['user_id']);
    
            
            include 'views/User/editprofile.php';
            break;
    case 'ask-question':
        if (!$isLoggedIn) {
            header('Location: index.php?page=login');
            exit;
        }
        include 'views/User/questions/create.php';
        break;
        
    case 'view-question':
        if (!isset($_GET['id'])) {
            header('Location: index.php');
            exit;
        }
        $question = $questionController->getQuestionById($_GET['id']);
        $answers = $answerController->getAnswersByQuestionId($_GET['id']);
        $tags = $tagController->getTagsForQuestion($_GET['id']);
        
        // Increment view count
        $questionController->incrementViewCount($_GET['id']);
        
        include 'views/User/questions/viewQues.php';
        break;
        
    case 'edit-question':
        if (!$isLoggedIn) {
            header('Location: index.php?page=login');
            exit;
        }
        
        if (!isset($_GET['id'])) {
            header('Location: index.php');
            exit;
        }
        
        $question = $questionController->getQuestionById($_GET['id']);
        
        // Only allow author or admin to edit
        if ($question['user_id'] != $_SESSION['user_id'] && !$isAdmin) {
            header('Location: index.php');
            exit;
        }
        
        $tags = $tagController->getTagsForQuestion($_GET['id']);
        include 'views/questions/edit.php';
        break;
        
    case 'delete-question':
        if (!$isLoggedIn) {
            header('Location: index.php?page=login');
            exit;
        }
        
        if (!isset($_GET['id'])) {
            header('Location: index.php');
            exit;
        }
        
        $question = $questionController->getQuestionById($_GET['id']);
        
        // Only allow author or admin to delete
        if ($question['user_id'] != $_SESSION['user_id'] && !$isAdmin) {
            header('Location: index.php');
            exit;
        }
        
        $questionController->deleteQuestion($_GET['id']);
        header('Location: index.php');
        exit;
        break;
        
    case 'vote':
        if (!$isLoggedIn) {
            echo json_encode(['success' => false, 'message' => 'يجب تسجيل الدخول للتصويت']);
            exit;
        }
        
        if (!isset($_POST['content_id']) || !isset($_POST['content_type']) || !isset($_POST['vote_type'])) {
            echo json_encode(['success' => false, 'message' => 'بيانات غير كاملة']);
            exit;
        }
        
        $contentId = $_POST['content_id'];
        $contentType = $_POST['content_type'];
        $voteType = $_POST['vote_type'];
        $userId = $_SESSION['user_id'];
        
        $result = $voteController->vote($contentId, $contentType, $userId, $voteType);
        
        // If vote successful, notify the content author
        if ($result['success']) {
            if ($voteType == 'up') {
                // Get content author id
                if ($contentType == 'question') {
                    $question = $questionController->getQuestionById($contentId);
                    if ($question && $question['user_id'] != $userId) {
                        $notificationController->notifyOnUpvote('question', $contentId, $question['user_id']);
                    }
                } else if ($contentType == 'answer') {
                    $answer = $answerController->getAnswerById($contentId);
                    if ($answer && $answer['user_id'] != $userId) {
                        $notificationController->notifyOnUpvote('answer', $contentId, $answer['user_id']);
                    }
                }
            }
        }
        
        echo json_encode($result);
        exit;
        break;
        
    case 'accept-answer':
        if (!$isLoggedIn) {
            echo json_encode(['success' => false, 'message' => 'يجب تسجيل الدخول لقبول الإجابة']);
            exit;
        }
        
        if (!isset($_POST['answer_id']) || !isset($_POST['question_id'])) {
            echo json_encode(['success' => false, 'message' => 'بيانات غير كاملة']);
            exit;
        }
        
        $answerId = $_POST['answer_id'];
        $questionId = $_POST['question_id'];
        
        // Check if user is question author
        $question = $questionController->getQuestionById($questionId);
        if ($question['user_id'] != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'فقط كاتب السؤال يمكنه قبول الإجابة']);
            exit;
        }
        
        $result = $questionController->acceptAnswer($questionId, $answerId);
        
        // If successful, notify the answer author
        if ($result['success']) {
            $answer = $answerController->getAnswerById($answerId);
            if ($answer && $answer['user_id'] != $_SESSION['user_id']) {
                $notificationController->notifyOnAcceptedAnswer($answerId, $answer['user_id'], $questionId);
            }
        }
        
        echo json_encode($result);
        exit;
        break;
        
    case 'add-answer':
        if (!$isLoggedIn) {
            header('Location: index.php?page=login');
            exit;
        }
        
        if (!isset($_POST['question_id']) || !isset($_POST['answer_content'])) {
            header('Location: index.php');
            exit;
        }
        
        $questionId = $_POST['question_id'];
        $content = $_POST['answer_content'];
        $userId = $_SESSION['user_id'];
        
        $result = $answerController->createAnswer($userId, $questionId, $content);
        
        header('Location: index.php?page=view-question&id=' . $questionId);
        exit;
        break;
        
    case 'edit-answer':
        if (!$isLoggedIn) {
            header('Location: index.php?page=login');
            exit;
        }
        
        if (!isset($_GET['id'])) {
            header('Location: index.php');
            exit;
        }
        
        $answer = $answerController->getAnswerById($_GET['id']);
        
        // Only allow author or admin to edit
        if ($answer['user_id'] != $_SESSION['user_id'] && !$isAdmin) {
            header('Location: index.php');
            exit;
        }
        
        include '../views/answers/edit.php';
        break;
        
    case 'delete-answer':
        if (!$isLoggedIn) {
            header('Location: index.php?page=login');
            exit;
        }
        
        if (!isset($_GET['id'])) {
            header('Location: index.php');
            exit;
        }
        
        $answer = $answerController->getAnswerById($_GET['id']);
        
        // Only allow author or admin to delete
        if ($answer['user_id'] != $_SESSION['user_id'] && !$isAdmin) {
            header('Location: index.php');
            exit;
        }
        
        $questionId = $answer['question_id'];
        $answerController->deleteAnswer($_GET['id']);
        
        header('Location: index.php?page=view-question&id=' . $questionId);
        exit;
        break;
        
    case 'search':
        if (!isset($_GET['q']) || empty($_GET['q'])) {
            header('Location: index.php');
            exit;
        }
        
        $query = $_GET['q'];
        $questions = $questionController->searchQuestions($query);
        include 'views/questions/search-results.php';
        break;
        
    case 'notifications':
        if (!$isLoggedIn) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $notifications = $notificationController->getUserNotifications($_SESSION['user_id'], 20);
        
        // Mark all as read
        $notificationController->markAllAsRead($_SESSION['user_id']);
        
        include 'views/users/notifications.php';
        break;
        
    case 'report-spam':
        if (!$isLoggedIn) {
            header('Location: index.php?page=login');
            exit;
        }
        
        include 'views/report-spam.php';
        break;
        
    case 'submit-spam-report':
        if (!$isLoggedIn) {
            header('Location: index.php?page=login');
            exit;
        }
        
        if (
            !isset($_POST['post_id']) || 
            !isset($_POST['post_type']) || 
            !isset($_POST['reason']) ||
            empty($_POST['reason'])
        ) {
            header('Location: index.php');
            exit;
        }
        
        $postId = $_POST['post_id'];
        $postType = $_POST['post_type'];
        $reason = $_POST['reason'];
        $reporterId = $_SESSION['user_id'];
        
        $result = $spammedPostController->reportSpam($postId, $postType, $reporterId, $reason);
        
        if ($result['success']) {
            if ($postType == 'question') {
                header('Location: index.php?page=view-question&id=' . $postId . '&report=success');
            } else {
                // Get question id for the answer
                $answer = $answerController->getAnswerById($postId);
                header('Location: index.php?page=view-question&id=' . $answer['question_id'] . '&report=success');
            }
        } else {
            if ($postType == 'question') {
                header('Location: index.php?page=view-question&id=' . $postId . '&report=error');
            } else {
                // Get question id for the answer
                $answer = $answerController->getAnswerById($postId);
                header('Location: index.php?page=view-question&id=' . $answer['question_id'] . '&report=error');
            }
        }
        exit;
        break;
        
    case 'groups':
        $groups = $groupController->getAllGroups();
        include 'views/User/groups.php';
        break;
        
    case 'view-group':
        if (!isset($_GET['id'])) {
            header('Location: index.php?page=groups');
            exit;
        }
        
        $group = $groupController->getGroupById($_GET['id']);
        $members = $groupController->getGroupMembers($_GET['id']);
        
        if ($isLoggedIn) {
            $isMember = $groupController->isGroupMember($_GET['id'], $_SESSION['user_id']);
        } else {
            $isMember = false;
        }
        
        include 'views/groups/view.php';
        break;
        
    case 'join-group':
        if (!$isLoggedIn) {
            header('Location: index.php?page=login');
            exit;
        }
        
        if (!isset($_GET['id'])) {
            header('Location: index.php?page=groups');
            exit;
        }
        
        $result = $groupController->joinGroup($_GET['id'], $_SESSION['user_id']);
        header('Location: index.php?page=view-group&id=' . $_GET['id']);
        exit;
        break;
        
    case 'leave-group':
        if (!$isLoggedIn) {
            header('Location: index.php?page=login');
            exit;
        }
        
        if (!isset($_GET['id'])) {
            header('Location: index.php?page=groups');
            exit;
        }
        
        $result = $groupController->leaveGroup($_GET['id'], $_SESSION['user_id']);
        header('Location: index.php?page=view-group&id=' . $_GET['id']);
        exit;
        break;
    
    case 'admin-dashboard':
        if (!$isAdmin) {
            header('Location: index.php');
            exit;
        }
        include 'views/admin/dashboard.php';
        break;
        
    case 'admin-users':
        if (!$isAdmin) {
            header('Location: index.php');
            exit;
        }
        // Get list of users
        $query = "SELECT * FROM users ORDER BY created_at DESC";
        $allUsers = $db->select($query);
        include 'views/admin/users.php';
        break;
        
    case 'admin-reports':
        if (!$isAdmin) {
            header('Location: index.php');
            exit;
        }
        include 'views/admin/reports.php';
        break;
        
    case 'admin-spammed':
        if (!$isAdmin) {
            header('Location: index.php');
            exit;
        }
        $spammedPosts = $spammedPostController->getAllSpammedPosts();
        include 'views/admin/spammed_posts.php';
        break;
        
    case 'generate-report':
        if (!$isAdmin) {
            header('Location: index.php');
            exit;
        }
        
        if (!isset($_GET['type'])) {
            header('Location: index.php?page=admin-reports');
            exit;
        }
        
        $reportType = $_GET['type'];
        
        switch ($reportType) {
            case 'users':
                $filename = $reportController->generateUserReport();
                break;
            case 'content':
                $filename = $reportController->generateContentReport();
                break;
            case 'activity':
                $days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
                $filename = $reportController->generateActivityReport($days);
                break;
            default:
                header('Location: index.php?page=admin-reports');
                exit;
        }
        
        header('Location: reports/' . $filename);
        exit;
        break;
        
    case 'admin-spammed-action':
        if (!$isAdmin) {
            header('Location: index.php');
            exit;
        }
        
        if (!isset($_GET['id']) || !isset($_GET['action'])) {
            header('Location: index.php?page=admin-spammed');
            exit;
        }
        
        $reportId = $_GET['id'];
        $action = $_GET['action'];
        
        if ($action == 'delete') {
            $spammedPostController->deleteSpammedContent($reportId);
        } else if ($action == 'reject') {
            $spammedPostController->updateSpamStatus($reportId, 'rejected');
        }
        
        header('Location: index.php?page=admin-spammed');
        exit;
        break;
        
    case 'admin-groups':
        if (!$isAdmin) {
            header('Location: index.php');
            exit;
        }
        $groups = $groupController->getAllGroups();
        include 'views/admin/group.php';
        break;
        
    case 'admin-create-group':
        if (!$isAdmin) {
            header('Location: index.php');
            exit;
        }
        
        if (isset($_POST['name']) && isset($_POST['description'])) {
            $result = $groupController->createGroup($_POST['name'], $_POST['description'], $_SESSION['user_id']);
            if ($result['success']) {
                header('Location: index.php?page=admin-groups');
                exit;
            }
        }
        
        include 'views/admin/create_group.php';
        break;
        
    case 'tags': // Add this new case for the tags page
            include 'views/User/tag.php';
            break; 
        
    default:
        include 'views/User/questions/indexQues.php';
        break;
}

// Include footer
include 'views/layouts/footer.php';

// Close database connection
$db->closeConnection();
?>