
<?php
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: index.php');
    exit;
}

// Make sure the database connection is available
if (!isset($db)) {
    // Create database connection if not already available
    $db = new DBController();
    $db->openConnection();
}

// Get stats for the dashboard
// Total users
$query = "SELECT COUNT(*) as total_users FROM users";
$totalUsers = $db->select($query)[0]['total_users'];

// Total questions
$query = "SELECT COUNT(*) as total_questions FROM questions";
$totalQuestions = $db->select($query)[0]['total_questions'];

// Total answers
$query = "SELECT COUNT(*) as total_answers FROM answers";
$totalAnswers = $db->select($query)[0]['total_answers'];

// Total tags
$query = "SELECT COUNT(*) as total_tags FROM tags";
$totalTags = $db->select($query)[0]['total_tags'];

// Recent reports
$query = "SELECT r.*, u.username as reporter_username, 
          CASE 
            WHEN r.question_id IS NOT NULL THEN 'سؤال'
            WHEN r.answer_id IS NOT NULL THEN 'إجابة'
            ELSE 'محتوى آخر'
          END as content_type
          FROM reports r
          LEFT JOIN users u ON r.user_id = u.id
          ORDER BY r.created_at DESC
          LIMIT 10";
$recentReports = $db->select($query);

// Recent signups
$query = "SELECT * FROM users ORDER BY created_at DESC LIMIT 5";
$recentUsers = $db->select($query);
?>

<h2 class="mb-4">لوحة التحكم الإدارية</h2>

<!-- Admin Navigation Links -->


<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">إجمالي المستخدمين</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalUsers; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">إجمالي الأسئلة</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalQuestions; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-question-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">إجمالي الإجابات</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalAnswers; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-comment fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">إجمالي التصنيفات</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalTags; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tags fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <h6 class="m-0 font-weight-bold">آخر البلاغات</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($recentReports)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>نوع المحتوى</th>
                                    <th>المُبلغ</th>
                                    <th>السبب</th>
                                    <th>التاريخ</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentReports as $report): ?>
                                    <tr>
                                        <td><?php echo $report['content_type']; ?></td>
                                        <td><?php echo htmlspecialchars($report['reporter_username']); ?></td>
                                        <td><?php echo htmlspecialchars($report['reason']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($report['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">عرض</button>
                                            <button class="btn btn-sm btn-outline-danger">حذف</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">لا توجد بلاغات جديدة</div>
                <?php endif; ?>
                
                <div class="text-center mt-3">
                    <a href="index.php?page=admin-reports" class="btn btn-outline-danger">عرض كل البلاغات</a>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="m-0 font-weight-bold">إحصائيات المنصة</h6>
            </div>
            <div class="card-body text-center">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <button class="btn btn-primary w-100">
                            <i class="fas fa-download me-1"></i>
                            تقرير النشاط (PDF)
                        </button>
                    </div>
                    <div class="col-md-6 mb-3">
                        <button class="btn btn-success w-100">
                            <i class="fas fa-chart-line me-1"></i>
                            تقرير المستخدمين (PDF)
                        </button>
                    </div>
                    <div class="col-md-6 mb-3">
                        <button class="btn btn-info w-100">
                            <i class="fas fa-question-circle me-1"></i>
                            تقرير الأسئلة (PDF)
                        </button>
                    </div>
                    <div class="col-md-6 mb-3">
                        <button class="btn btn-warning w-100">
                            <i class="fas fa-tag me-1"></i>
                            تقرير التصنيفات (PDF)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="m-0 font-weight-bold">آخر المستخدمين المسجلين</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($recentUsers)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>اسم المستخدم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>تاريخ التسجيل</th>
                                    <th>السمعة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentUsers as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                        <td><?php echo $user['reputation']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">لا يوجد مستخدمون جدد</div>
                <?php endif; ?>
                
                <div class="text-center mt-3">
                    <a href="index.php?page=admin-users" class="btn btn-outline-primary">عرض كل المستخدمين</a>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h6 class="m-0 font-weight-bold">إدارة المجموعات</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <a href="index.php?page=admin-create-group" class="btn btn-success mb-3 w-100">
                        <i class="fas fa-plus-circle me-1"></i> إنشاء مجموعة جديدة
                    </a>
                </div>
                
                <h6 class="mb-3">المجموعات الأخيرة</h6>
                <?php 
                // Get the most recent groups (limited to 3 for display)
                $query = "SELECT g.*, (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as member_count 
                          FROM groups g ORDER BY g.created_at DESC LIMIT 3";
                $recentGroups = $db->select($query);
                
                if (!empty($recentGroups)):
                ?>
                <ul class="list-group">
                    <?php foreach ($recentGroups as $group): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo htmlspecialchars($group['name']); ?>
                        <span class="badge bg-primary rounded-pill"><?php echo $group['member_count']; ?> عضو</span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="alert alert-info">لا توجد مجموعات للعرض</div>
                <?php endif; ?>
                
                <div class="text-center mt-3">
                    <a href="index.php?page=admin-groups" class="btn btn-outline-success">إدارة المجموعات</a>
                </div>
            </div>
        </div>
    </div>
</div>