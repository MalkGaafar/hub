
<!DOCTYPE html>
<html lang="<?php echo isset($_SESSION['language']) ? $_SESSION['language'] : 'ar'; ?>" dir="<?php echo isset($_SESSION['language']) && $_SESSION['language'] == 'en' ? 'ltr' : 'rtl'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منصة المعرفة العربية - المساحة المثالية للأسئلة والإجابات</title>
    
    <?php if (isset($_SESSION['language']) && $_SESSION['language'] == 'en'): ?>
        <!-- Bootstrap LTR CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <?php else: ?>
        <!-- Bootstrap RTL CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <?php endif; ?>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Language-specific CSS -->
    <?php if (isset($_SESSION['language']) && $_SESSION['language'] == 'en'): ?>
        <style>
            body {
                text-align: left;
                direction: ltr;
            }
        </style>
    <?php endif; ?>
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-custom {
            background-color: #7952b3;
        }
        .navbar-brand, .navbar-nav .nav-link {
            color: white !important;
        }
        .navbar-brand:hover, .navbar-nav .nav-link:hover {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        .btn-primary {
            background-color: #7952b3;
            border-color: #7952b3;
        }
        .btn-primary:hover {
            background-color: #614092;
            border-color: #614092;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .question-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .tag {
            background-color: #e9ecef;
            color: #495057;
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
            border-radius: 50px;
            margin-right: 0.5rem;
            display: inline-block;
            margin-bottom: 0.5rem;
        }
        .reputation {
            color: #28a745;
            font-weight: bold;
        }
        .answer-count {
            background-color: #28a745;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
        }
        .vote-buttons button {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #6c757d;
            cursor: pointer;
        }
        .vote-buttons button:hover {
            color: #7952b3;
        }
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            text-align: center;
            line-height: 18px;
        }
        .language-switcher {
            margin-left: 15px;
        }
        
        /* Adjust for left-to-right languages */
        html[dir="ltr"] .notification-badge {
            right: auto;
            left: 0;
        }
        html[dir="ltr"] .language-switcher {
            margin-left: 0;
            margin-right: 15px;
        }
    </style>
</head>
<body>
<?php
ob_start();
?>
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <?php echo isset($_SESSION['language']) && $_SESSION['language'] == 'en' ? 'Arabic Knowledge Platform' : 'منصة المعرفة العربية'; ?>
        </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <?php echo isset($_SESSION['language']) && $_SESSION['language'] == 'en' ? 'Home' : 'الرئيسية'; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=ask-question">
                            <?php echo isset($_SESSION['language']) && $_SESSION['language'] == 'en' ? 'Ask Question' : 'اطرح سؤالاً'; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=groups">
                            <?php echo isset($_SESSION['language']) && $_SESSION['language'] == 'en' ? 'Groups' : 'المجموعات'; ?>
                        </a>
                    </li>
                 
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=tags">
                            <?php echo isset($_SESSION['language']) && $_SESSION['language'] == 'en' ? 'Tags' : 'التصنيفات'; ?>
                        </a>
                    </li>

                </ul>
                
                <form class="d-flex mx-auto" action="index.php" method="GET">
                    <input type="hidden" name="page" value="search">
                    <input class="form-control me-2" type="search" name="q" placeholder="<?php echo isset($_SESSION['language']) && $_SESSION['language'] == 'en' ? 'Search for a question...' : 'ابحث عن سؤال...'; ?>" aria-label="Search">
                    <button class="btn btn-light" type="submit">
                        <?php echo isset($_SESSION['language']) && $_SESSION['language'] == 'en' ? 'Search' : 'بحث'; ?>
                    </button>
                </form>
                
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <!-- Language Switcher -->
                    <li class="nav-item">
                        <div class="language-switcher">
                            <a href="index.php?page=switch-language&lang=<?php echo isset($_SESSION['language']) && $_SESSION['language'] == 'en' ? 'ar' : 'en'; ?>" class="btn btn-sm btn-outline-light">
                                <?php echo isset($_SESSION['language']) && $_SESSION['language'] == 'en' ? 'العربية' : 'English'; ?>
                            </a>
                        </div>
                    </li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Notifications -->
                        <li class="nav-item dropdown">
                            <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                <?php 
                                // Get unread notifications count
                                if (isset($notificationController)) {
                                    $unreadCount = $notificationController->getUnreadNotificationsCount($_SESSION['user_id']);
                                    if ($unreadCount > 0) {
                                        echo '<span class="notification-badge">' . $unreadCount . '</span>';
                                    }
                                }
                                ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                                <?php 
                                if (isset($notificationController)) {
                                    $notifications = $notificationController->getUserNotifications($_SESSION['user_id'], 5);
                                    if (!empty($notifications)) {
                                        foreach ($notifications as $notification) {
                                            echo '<li><a class="dropdown-item" href="#"><small>' . 
                                                htmlspecialchars($notification['message']) . 
                                                '<br><span class="text-muted">' . 
                                                date('Y-m-d H:i', strtotime($notification['created_at'])) . 
                                                '</span></small></a></li>';
                                        }
                                        echo '<li><hr class="dropdown-divider"></li>';
                                        echo '<li><a class="dropdown-item text-center" href="index.php?page=notifications">عرض الكل</a></li>';
                                    } else {
                                        echo '<li><span class="dropdown-item text-muted">لا توجد إشعارات</span></li>';
                                    }
                                } else {
                                    echo '<li><span class="dropdown-item text-muted">لا توجد إشعارات</span></li>';
                                }
                                ?>
                            </ul>
                        </li>
                        
                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=admin-dashboard">
                                <?php echo isset($_SESSION['language']) && $_SESSION['language'] == 'en' ? 'Admin Dashboard' : 'لوحة الإدارة'; ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=profile">
                                <i class="fas fa-user"></i>
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=logout">
                                <?php echo isset($_SESSION['language']) && $_SESSION['language'] == 'en' ? 'Logout' : 'تسجيل خروج'; ?>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=login">
                                <?php echo isset($_SESSION['language']) && $_SESSION['language'] == 'en' ? 'Login' : 'تسجيل دخول'; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=register">
                                <?php echo isset($_SESSION['language']) && $_SESSION['language'] == 'en' ? 'Register' : 'حساب جديد'; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">