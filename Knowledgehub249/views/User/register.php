

<head>
<?php if (isset($_SESSION['language']) && $_SESSION['language'] == 'en'): ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <?php else: ?>
        <!-- Bootstrap RTL CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <?php endif; ?>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">إنشاء حساب جديد</h4>
            </div>
            <div class="card-body">
                <?php if (isset($_POST['register'])): ?>
                    <?php
                    // Process registration form
                    if (
                        isset($_POST['username']) && !empty($_POST['username']) &&
                        isset($_POST['email']) && !empty($_POST['email']) &&
                        isset($_POST['password']) && !empty($_POST['password']) &&
                        isset($_POST['confirm_password']) && !empty($_POST['confirm_password'])
                    ) {
                        $username = $_POST['username'];
                        $email = $_POST['email'];
                        $password = $_POST['password'];
                        $confirmPassword = $_POST['confirm_password'];
                        
                        if ($password !== $confirmPassword) {
                            echo '<div class="alert alert-danger">كلمة المرور غير متطابقة</div>';
                        } else {
                            $result = $userController->register($username, $email, $password);
                            
                            if ($result['success']) {
                                echo '<div class="alert alert-success">' . $result['message'] . ' جاري التحويل...</div>';
                                echo '<script>setTimeout(function() { window.location.href = "index.php"; }, 2000);</script>';
                            } else {
                                echo '<div class="alert alert-danger">' . $result['message'] . '</div>';
                            }
                        }
                    } else {
                        echo '<div class="alert alert-danger">جميع الحقول مطلوبة</div>';
                    }
                    ?>
                <?php endif; ?>
                
                <form method="post" action="index.php?page=register">
                    <div class="mb-3">
                        <label for="username" class="form-label">اسم المستخدم</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">تأكيد كلمة المرور</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الاهتمامات (اختياري)</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="programming" id="programming" name="interests[]">
                            <label class="form-check-label" for="programming">
                                البرمجة
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="data_science" id="data_science" name="interests[]">
                            <label class="form-check-label" for="data_science">
                                علوم البيانات
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="ai" id="ai" name="interests[]">
                            <label class="form-check-label" for="ai">
                                الذكاء الاصطناعي
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="web_dev" id="web_dev" name="interests[]">
                            <label class="form-check-label" for="web_dev">
                                تطوير الويب
                            </label>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" name="register" class="btn btn-primary">إنشاء حساب</button>
                    </div>
                </form>
                <div class="mt-3 text-center">
                    لديك حساب بالفعل؟ <a href="index.php?page=login">تسجيل دخول</a>
                </div>
            </div>
        </div>
    </div>
</div>

