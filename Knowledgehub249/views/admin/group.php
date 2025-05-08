<?php
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: index.php');
    exit;
}


// Get group controller if not already available
if (!isset($groupController)) {
    $groupController = new GroupController($db);
}

// Handle group creation
if (isset($_POST['create_group'])) {
    if (!empty($_POST['group_name']) && !empty($_POST['group_description'])) {
        $result = $groupController->createGroup(
            $_POST['group_name'],
            $_POST['group_description'],
            $_SESSION['user_id']
        );
        
        if ($result['success']) {
            echo '<div class="alert alert-success">' . $result['message'] . '</div>';
        } else {
            echo '<div class="alert alert-danger">' . $result['message'] . '</div>';
        }
    } else {
        echo '<div class="alert alert-danger">جميع الحقول مطلوبة</div>';
    }
}

// Handle group deletion
if (isset($_GET['delete_group']) && is_numeric($_GET['delete_group'])) {
    $groupId = (int)$_GET['delete_group'];
    
    // Check if the group exists first
    $group = $groupController->getGroupById($groupId);
    
    if ($group) {
        // Implement a delete method in GroupController if it doesn't exist
        $query = "DELETE FROM groups WHERE id = $groupId";
        $result = $db->delete($query);
        
        // Also delete group members
        $query = "DELETE FROM group_members WHERE group_id = $groupId";
        $db->delete($query);
        
        if ($result) {
            echo '<div class="alert alert-success">تم حذف المجموعة بنجاح</div>';
        } else {
            echo '<div class="alert alert-danger">حدث خطأ أثناء حذف المجموعة</div>';
        }
    }
}

// Get all groups
$groups = $groupController->getAllGroups();

// Get group members if viewing a specific group
$groupMembers = null;
$currentGroup = null;
if (isset($_GET['view_members']) && is_numeric($_GET['view_members'])) {
    $groupId = (int)$_GET['view_members'];
    $currentGroup = $groupController->getGroupById($groupId);
    
    if ($currentGroup) {
        $groupMembers = $groupController->getGroupMembers($groupId);
    }
}
?>

<h2 class="mb-4">إدارة المجموعات</h2>

<?php if ($groupMembers && $currentGroup): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">أعضاء مجموعة: <?php echo htmlspecialchars($currentGroup['name']); ?></h6>
            <a href="index.php?page=admin-groups" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> العودة للمجموعات
            </a>
        </div>
        <div class="card-body">
            <?php if (!empty($groupMembers)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>اسم المستخدم</th>
                                <th>البريد الإلكتروني</th>
                                <th>السمعة</th>
                                <th>المسؤول</th>
                                <th>تاريخ الانضمام</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($groupMembers as $member): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($member['username']); ?></td>
                                    <td><?php echo htmlspecialchars($member['email']); ?></td>
                                    <td><?php echo $member['reputation']; ?></td>
                                    <td>
                                        <?php if ($member['is_admin'] == 1): ?>
                                            <span class="badge bg-success">نعم</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">لا</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($member['joined_at'])); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="index.php?page=view-profile&id=<?php echo $member['user_id']; ?>" class="btn btn-sm btn-outline-primary">عرض</a>
                                            <a href="index.php?page=admin-groups&remove_member=<?php echo $member['user_id']; ?>&group_id=<?php echo $currentGroup['id']; ?>" class="btn btn-sm btn-outline-danger">إزالة</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">لا يوجد أعضاء في هذه المجموعة</div>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">إنشاء مجموعة جديدة</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="index.php?page=admin-groups">
                        <div class="mb-3">
                            <label for="group_name" class="form-label">اسم المجموعة</label>
                            <input type="text" class="form-control" id="group_name" name="group_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="group_description" class="form-label">وصف المجموعة</label>
                            <textarea class="form-control" id="group_description" name="group_description" rows="3" required></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="create_group" class="btn btn-success">إنشاء مجموعة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">المجموعات الحالية</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($groups)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>اسم المجموعة</th>
                                        <th>الوصف</th>
                                        <th>المنشئ</th>
                                        <th>عدد الأعضاء</th>
                                        <th>تاريخ الإنشاء</th>
                                        <th>إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($groups as $group): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($group['name']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($group['description'], 0, 50)) . (strlen($group['description']) > 50 ? '...' : ''); ?></td>
                                            <td><?php echo htmlspecialchars($group['creator_name']); ?></td>
                                            <td><?php echo $group['member_count']; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($group['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="index.php?page=admin-groups&view_members=<?php echo $group['id']; ?>" class="btn btn-sm btn-outline-primary">الأعضاء</a>
                                                    <a href="index.php?page=admin-groups&delete_group=<?php echo $group['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من حذف هذه المجموعة؟')">حذف</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">لا توجد مجموعات</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
