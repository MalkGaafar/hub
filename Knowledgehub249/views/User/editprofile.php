php
<?php
session_start(); // Start the session if it's not started in a common include

// Include necessary files (adjust paths as needed)
require_once 'controllers/DBController.php';
require_once 'controllers/userController.php';

// Ensure the database connection is established
if (!isset($db)) {
    $db = new DBController();
    $db->openConnection();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: index.php?page=login');
    exit;
}

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from the POST request
    $username = $_POST['username'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $confirm_password = $_POST['confirm_password'] ?? null;
    $language = $_POST['language'] ?? null;

    // Validate data (basic validation, add more as needed)
    if (empty($username) || empty($email) || empty($language)) {
        // Handle missing data error
        $_SESSION['error_message'] = 'البيانات المطلوبة مفقودة.';
        header('Location: index.php?page=profile'); // Redirect back to profile page
        exit;
    }

    if ($password && $password !== $confirm_password) {
        // Handle password mismatch error
        $_SESSION['error_message'] = 'كلمات المرور غير متطابقة.';
        header('Location: index.php?page=profile'); // Redirect back to profile page
        exit;
    }

    // Sanitize data (using native PHP)
    $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); // Example sanitization
    $email = filter_var($email, FILTER_SANITIZE_EMAIL); // Example sanitization

    // Prepare data array for UserController::updateProfile
    $updateData = [
        'username' => $username,
        'email' => $email,
        'language' => $language
    ];

    if ($password) {
        // Hash the new password using native PHP's password_hash
        $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    $userId = $_SESSION['user_id'];
    $userController = new UserController($db);

    // Call the updateProfile method
    $updateResult = $userController->updateProfile($userId, $updateData);

    if ($updateResult) {
        // Handle successful update
        $_SESSION['success_message'] = 'تم تحديث الملف الشخصي بنجاح!';
        // Optionally, update session variables if username or language changed
         $_SESSION['username'] = $username;
         $_SESSION['language'] = $language;

    } else {
        // Handle update failure
        $_SESSION['error_message'] = 'فشل تحديث الملف الشخصي.';
    }

    // Redirect back to the profile page after processing
    header('Location: index.php?page=profile');
    exit;

} else {
    // If accessed directly without POST, redirect or show an error
    header('Location: index.php?page=profile'); // Redirect back to profile page
    exit;
}
?>
