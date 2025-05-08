
<?php
// views/admin/users.php

// Ensure admin is logged in and has permissions
// (You should have this check at the beginning of your admin pages)
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: index.php?page=login'); // Redirect to login if not admin
    exit;
}

// Include necessary files (adjust paths as needed)


// Create database connection (if not already established)
$db = new DBController();
$db->openConnection();

// Create UserController instance

// Handle user deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete_user' && isset($_GET['id'])) {
    $userIdToDelete = (int)$_GET['id'];
    // Prevent admin from deleting themselves
    if ($userIdToDelete !== $_SESSION['user_id']) {
        $success = $userController->deleteUser($userIdToDelete); // Assume you have a deleteUser method in UserController

        if ($success) {
            // Redirect to the users page after deletion
            header('Location: index.php?page=admin&view=users&status=deleted');
            exit;
        } else {
            $error = "Failed to delete user.";
        }
    } else {
        $error = "You cannot delete your own admin account.";
    }
}

// Get all users
 // Assume you have a getAllUsers method in UserController


?>

<div class="container mt-4">
    <h2>إدارة المستخدمين</h2>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'deleted'): ?>
        <div class="alert alert-success">تم حذف المستخدم بنجاح.</div>
    <?php elseif (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>اسم المستخدم</th>
                <th>البريد الإلكتروني</th>
                <th>السمعة</th>
                <th>تاريخ التسجيل</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($allUsers)): ?>
                <?php foreach ($allUsers as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo $user['reputation']; ?></td>
                        <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <a href="index.php?page=admin&view=users&action=delete_user&id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد أنك تريد حذف هذا المستخدم؟');">حذف</a>
                            <!-- Add other actions like edit if needed -->
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">لا يوجد مستخدمون لعرضهم.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
