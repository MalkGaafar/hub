

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
                <h4 class="mb-0">تسجيل الدخول</h4>
            </div>
            <div class="card-body">
                <?php if (isset($_POST['login'])): ?>
                    <?php
                    // Process login form
                    if (
                        isset($_POST['email']) && !empty($_POST['email']) &&
                        isset($_POST['password']) && !empty($_POST['password'])
                    ) {
                        $email = $_POST['email'];
                        $password = $_POST['password'];
                        
                        $result = $userController->login($email, $password);
                        
                        if ($result['success']) {
                            echo '<div class="alert alert-success">' . $result['message'] . ' جاري التحويل...</div>';
                            $redirectTo = $result['isAdmin'] ? 'index.php?page=admin-dashboard' : 'index.php';
                            echo '<script>setTimeout(function() { window.location.href = "' . $redirectTo . '"; }, 2000);</script>';
                        } else {
                            echo '<div class="alert alert-danger">' . $result['message'] . '</div>';
                        }
                    } else {
                        echo '<div class="alert alert-danger">جميع الحقول مطلوبة</div>';
                    }
                    ?>
                <?php endif; ?>
                
                <form method="post" action="index.php?page=login">
                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                        <label class="form-check-label" for="remember_me">تذكرني</label>
                    </div>
                    <div class="d-grid">
                        <button type="submit" name="login" class="btn btn-primary">تسجيل الدخول</button>                       
                    </div>
                    <div class="d-grid mt-2">
                         <button type="button" id="register-button" class="btn btn-secondary">إنشاء حساب جديد</button>
                    </div>                   
                </form>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('register-button').addEventListener('click', function() {
            window.location.href = 'index.php?page=register';
        });
    });   
</script>
    <script src="../assets/js/main.js"></script>
</div>






