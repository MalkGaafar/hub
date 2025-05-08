<?php

// Make sure the database connection is established
if (!isset($db)) {
    // Create database connection if not already available
    $db = new DBController();
    $db->openConnection();
}

// Get the current user's information if the user variable isn't set
if (!isset($user) && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $userController = new UserController($db);
    $user = $userController->getUserById($userId);
} else if (!isset($user)) {
    // If there's no user_id in session, redirect to login
    header('Location: index.php?page=login');
    exit;
}

// Make sure $user is valid before continuing
if (!$user || !is_array($user)) {
    echo '<div class="alert alert-danger">User information could not be loaded.</div>';
    exit;
}
?>

<div class="row">
    <div class="col-md-4">
        <!-- Main User Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <!-- User Name and Join Date -->
                    <div>
                        <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                        <p class="text-muted mb-0">
                            <i class="fas fa-calendar-alt me-1"></i>
                            عضو منذ <?php echo date('d M Y', strtotime($user['created_at'])); ?>
                        </p>
                    </div>
                    
                    <!-- User Reputation -->
                    <div class="text-center">
                        <div class="fs-4 fw-bold"><?php echo number_format($user['reputation']); ?></div>
                        <div class="small text-muted">نقطة سمعة</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Badges Card -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">الشارات</h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <?php if ($user['reputation'] >= 25000): // Gold threshold ?>
                    <div class="col-12">
                        <div class="d-flex align-items-center badge-item p-2 border rounded">
                            <span class="badge rounded-pill bg-warning me-2">
                                <i class="fas fa-medal"></i>
                            </span>
                            <span>الوسام الذهبي</span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($user['reputation'] >= 5000): // Silver threshold ?>
                    <div class="col-12">
                        <div class="d-flex align-items-center badge-item p-2 border rounded">
                            <span class="badge rounded-pill bg-secondary me-2"> <!-- Changed from bg-primary to bg-secondary for silver -->
                                <i class="fas fa-medal"></i>
                            </span>
                            <span>الوسام الفضي</span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($user['reputation'] >= 500): // Bronze threshold ?>
                    <div class="col-12">
                        <div class="d-flex align-items-center badge-item p-2 border rounded">
                            <span class="badge rounded-pill bg-danger me-2"> <!-- Using bg-danger for bronze color -->
                                <i class="fas fa-medal"></i>
                            </span>
                            <span>الوسام البرونزي</span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($user['reputation'] < 500): // No badges yet ?>
                    <div class="col-12">
                        <p class="text-muted mb-0">لا توجد أوسمة حتى الآن</p>
                        <small class="text-muted">اكسب 500 نقطة للحصول على الوسام البرونزي</small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Stats Card -->
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">إحصائيات</h5>
            </div>
            <div class="card-body">
                <?php
                // Get counts for questions and answers
                $questionCount = $db->selectOne("SELECT COUNT(*) as count FROM questions WHERE user_id = " . $user['id'])['count'] ?? 0;
                $answerCount = $db->selectOne("SELECT COUNT(*) as count FROM answers WHERE user_id = " . $user['id'])['count'] ?? 0;
                $acceptedCount = $db->selectOne("SELECT COUNT(*) as count FROM answers WHERE user_id = " . $user['id'] . " AND is_accepted = 1")['count'] ?? 0;
                ?>
                
                <div class="row text-center">
                    <div class="col-4">
                        <div class="fs-4"><?php echo $questionCount; ?></div>
                        <div class="small text-muted">أسئلة</div>
                    </div>
                    <div class="col-4">
                        <div class="fs-4"><?php echo $answerCount; ?></div>
                        <div class="small text-muted">إجابات</div>
                    </div>
                    <div class="col-4">
                        <div class="fs-4"><?php echo $acceptedCount; ?></div>
                        <div class="small text-muted">مقبولة</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="questions-tab" data-bs-toggle="tab" data-bs-target="#questions" type="button" role="tab" aria-controls="questions" aria-selected="true">أسئلتي</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="answers-tab" data-bs-toggle="tab" data-bs-target="#answers" type="button" role="tab" aria-controls="answers" aria-selected="false">إجاباتي</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="saved-tab" data-bs-toggle="tab" data-bs-target="#saved" type="button" role="tab" aria-controls="saved" aria-selected="false">المحفوظات</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false">الإعدادات</button>
            </li>
        </ul>

        <div class="tab-content p-3 border border-top-0 rounded-bottom" id="profileTabsContent">
            <div class="tab-pane fade show active" id="questions" role="tabpanel" aria-labelledby="questions-tab">
                <?php
                // Get user's questions
                if (isset($user['id'])) {
                    $query = "SELECT q.*, 
                             (SELECT COUNT(*) FROM answers WHERE question_id = q.id) as answer_count
                             FROM questions q 
                             WHERE q.user_id = " . $user['id'] . " 
                             ORDER BY q.created_at DESC";
                    $userQuestions = $db->select($query);

                    if (!empty($userQuestions)) {
                        foreach ($userQuestions as $question) {
                            echo '<div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="me-3 text-center">
                                                <div class="votes-count mb-2">
                                                    <span class="fs-5">' . ($question['votes'] ?? 0) . '</span>
                                                    <div class="small text-muted">تصويتات</div>
                                                </div>
                                                <div class="answer-count">
                                                    <span class="fs-5">' . $question['answer_count'] . '</span>
                                                    <div class="small text-muted">إجابات</div>
                                                </div>
                                            </div>
                                            <div>
                                                <h5 class="card-title">
                                                    <a href="index.php?page=view-question&id=' . $question['id'] . '">
                                                        ' . htmlspecialchars($question['title']) . '
                                                    </a>
                                                </h5>
                                                <p class="card-text">' . substr(htmlspecialchars($question['content']), 0, 150) . '...</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">' . date('d M Y', strtotime($question['created_at'])) . '</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>';
                        }
                    } else {
                        echo '<div class="alert alert-info">لم تقم بطرح أي أسئلة بعد.</div>';
                    }
                } else {
                    echo '<div class="alert alert-danger">لا يمكن تحميل الأسئلة.</div>';
                }
                ?>
            </div>

            <div class="tab-pane fade" id="answers" role="tabpanel" aria-labelledby="answers-tab">
                <?php
                // Get user's answers
                if (isset($user['id'])) {
                    $query = "SELECT a.*, q.title as question_title, q.id as question_id,
                             (SELECT COUNT(*) FROM answers WHERE question_id = q.id) as answer_count
                             FROM answers a
                             JOIN questions q ON a.question_id = q.id
                             WHERE a.user_id = " . $user['id'] . "
                             ORDER BY a.created_at DESC";
                    $userAnswers = $db->select($query);

                    if (!empty($userAnswers)) {
                        foreach ($userAnswers as $answer) {
                            echo '<div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="me-3 text-center">
                                                <div class="votes-count mb-2">
                                                    <span class="fs-5">' . ($answer['votes'] ?? 0) . '</span>
                                                    <div class="small text-muted">تصويتات</div>
                                                </div>
                                                ' . ($answer['is_accepted'] ? '<div class="text-success"><i class="fas fa-check-circle"></i> مقبولة</div>' : '') . '
                                            </div>
                                            <div>
                                                <h6 class="card-subtitle mb-2">
                                                    <a href="index.php?page=view-question&id=' . $answer['question_id'] . '">
                                                        ' . htmlspecialchars($answer['question_title']) . '
                                                    </a>
                                                </h6>
                                                <p class="card-text">' . substr(htmlspecialchars($answer['content']), 0, 150) . '...</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">' . date('d M Y', strtotime($answer['created_at'])) . '</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>';
                        }
                    } else {
                        echo '<div class="alert alert-info">لم تقم بالإجابة على أي أسئلة بعد.</div>';
                    }
                } else {
                    echo '<div class="alert alert-danger">لا يمكن تحميل الإجابات.</div>';
                }
                ?>
            </div>

            <div class="tab-pane fade" id="saved" role="tabpanel" aria-labelledby="saved-tab">
                <div class="alert alert-info">لا توجد أسئلة محفوظة.</div>
            </div>

            <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
    <?php
    // Display success or error messages from session
    if (isset($_SESSION['profile_message'])) {
        $messageClass = $_SESSION['profile_success'] ? 'alert-success' : 'alert-danger';
        echo '<div class="alert ' . $messageClass . '">' . htmlspecialchars($_SESSION['profile_message']) . '</div>';
        unset($_SESSION['profile_message']);
        unset($_SESSION['profile_success']);
    }
    ?>
<form id="profile-settings-form" method="POST" action="editprofile.php">
    <div class="mb-3">
        <label for="update_username" class="form-label">اسم المستخدم</label>
        <input type="text" class="form-control" id="update_username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
    </div>
    <div class="mb-3">
        <label for="update_email" class="form-label">البريد الإلكتروني</label>
        <input type="email" class="form-control" id="update_email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
    </div>
    <div class="mb-3">
        <label for="update_password" class="form-label">كلمة المرور الجديدة</label>
        <input type="password" class="form-control" id="update_password" name="password">
    </div>
    <div class="mb-3">
        <label for="confirm_update_password" class="form-label">تأكيد كلمة المرور</label>
        <input type="password" class="form-control" id="confirm_update_password" name="confirm_password">
    </div>
    <div class="mb-3">
        <label class="form-label d-block">اللغة المفضلة</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="language" id="language_ar" value="ar" <?php echo isset($_SESSION['language']) && $_SESSION['language'] == 'ar' ? 'checked' : ''; ?>>
            <label class="form-check-label" for="language_ar">العربية</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="language" id="language_en" value="en" <?php echo isset($_SESSION['language']) && $_SESSION['language'] == 'en' ? 'checked' : ''; ?>>
            <label class="form-check-label" for="language_en">English</label>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
</form>

<!-- Make sure there is NO JavaScript code preventing default form submission here -->

</div>
            </div>
        </div>
    </div>
</div>

<style>
/* Additional styles for Stack Overflow look */
.badge-item {
    transition: all 0.2s ease;
}
.badge-item:hover {
    background-color: #f8f9fa;
}
.votes-count, .answer-count {
    min-width: 60px;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profile-settings-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const username = document.getElementById('update_username').value;
            const email = document.getElementById('update_email').value;
            const password = document.getElementById('update_password').value;
            const confirmPassword = document.getElementById('confirm_update_password').value;
            const language = document.querySelector('input[name="language"]:checked').value;

            if (password && password !== confirmPassword) {
                alert('كلمات المرور غير متطابقة');
                return;
            }

            const data = {
                username: username,
                email: email,
                language: language
            };

            if (password) {
                data.password = password;
            }

            fetch('index.php?action=update_profile', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('تم حفظ التغييرات بنجاح');
                    // Optionally, update the displayed profile information without a full page reload
                } else {
                    alert('خطأ في حفظ التغييرات: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ غير متوقع.');
            });
        });
    }
});
</script>

