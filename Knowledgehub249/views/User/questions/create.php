

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
    <div class="col-md-10">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">اطرح سؤالاً</h4>
            </div>
            <div class="card-body">
                <?php if (isset($_POST['submit_question'])): ?>
                    <?php
                    if (
                        isset($_POST['title']) && !empty($_POST['title']) &&
                        isset($_POST['content']) && !empty($_POST['content'])
                    ) {
                        $title = $_POST['title'];
                        $content = $_POST['content'];
                        $tags = isset($_POST['tags']) ? explode(',', $_POST['tags']) : [];
                        
                        $userId = $_SESSION['user_id'];
                        $questionId = $questionController->createQuestion($userId, $title, $content, $tags);
                        
                        if ($questionId) {
                            echo '<div class="alert alert-success">تم نشر سؤالك بنجاح! جاري التحويل...</div>';
                            echo '<script>setTimeout(function() { window.location.href = "index.php?page=view-question&id=' . $questionId . '"; }, 2000);</script>';
                        } else {
                            echo '<div class="alert alert-danger">حدث خطأ أثناء نشر السؤال</div>';
                        }
                    } else {
                        echo '<div class="alert alert-danger">العنوان والمحتوى مطلوبان</div>';
                    }
                    ?>
                <?php endif; ?>
                
                <form method="post" action="index.php?page=ask-question">
                    <div class="mb-3">
                        <label for="title" class="form-label">عنوان السؤال</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="اكتب عنواناً واضحاً ومختصراً لسؤالك" required>
                        <div class="form-text">عنوان جيد يساعد في الحصول على إجابات أفضل</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">تفاصيل السؤال</label>
                        <textarea class="form-control" id="content" name="content" rows="10" placeholder="اشرح سؤالك بالتفصيل. أضف الأكواد البرمجية أو أي معلومات إضافية تساعد في فهم المشكلة" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tags" class="form-label">التصنيفات</label>
                        <input type="text" class="form-control" id="tags" name="tags" placeholder="أدخل التصنيفات مفصولة بفواصل (مثال: php,mysql,html)">
                        <div class="form-text">أضف تصنيفات ذات صلة بسؤالك لمساعدة الآخرين في العثور عليه</div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h5>نصائح للحصول على إجابات جيدة:</h5>
                        <ul>
                            <li>تأكد من أن سؤالك ليس مكرراً - ابحث قبل النشر</li>
                            <li>اكتب عنواناً واضحاً وموجزاً</li>
                            <li>اشرح المشكلة بوضوح وأضف ما حاولت فعله لحلها</li>
                            <li>أضف أي أكواد برمجية ذات صلة</li>
                            <li>أضف التصنيفات المناسبة ليسهل العثور على سؤالك</li>
                        </ul>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" name="submit_question" class="btn btn-primary">نشر السؤال</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

