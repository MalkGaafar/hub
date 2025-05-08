<?php
// Make sure the database connection is available
if (!isset($db)) {
    // Create database connection if not already available
    $db = new DBController();
    $db->openConnection();
}

// Define text content based on language
$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'ar';
$text = [
    'ar' => [
        'latest_questions' => 'آخر الأسئلة',
        'ask_question' => 'اطرح سؤالاً',
        'votes' => 'تصويت',
        'answers' => 'إجابة',
        'asked' => 'سُئل',
        'no_questions' => 'لا توجد أسئلة حتى الآن. كن أول من يطرح سؤالاً!',
        'popular_tags' => 'التصنيفات الشائعة',
        'no_tags' => 'لا توجد تصنيفات بعد',
        'active_users' => 'المستخدمون النشطون',
        'no_active_users' => 'لا يوجد مستخدمون نشطون بعد'
    ],
    'en' => [
        'latest_questions' => 'Latest Questions',
        'ask_question' => 'Ask a Question',
        'votes' => 'votes',
        'answers' => 'answers',
        'asked' => 'Asked',
        'no_questions' => 'No questions yet. Be the first to ask!',
        'popular_tags' => 'Popular Tags',
        'no_tags' => 'No tags yet',
        'active_users' => 'Active Users',
        'no_active_users' => 'No active users yet'
    ]
];
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo ($lang == 'ar') ? 'rtl' : 'ltr'; ?>">
<head>
    <?php if ($lang == 'en'): ?>
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
<body>

<div class="row">
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><?php echo $text[$lang]['latest_questions']; ?></h2>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php?page=ask-question" class="btn btn-primary">
                    <i class="fas fa-plus-circle <?php echo ($lang == 'ar') ? 'ms-1' : 'me-1'; ?>"></i> 
                    <?php echo $text[$lang]['ask_question']; ?>
                </a>
            <?php endif; ?>
        </div>
        
        <?php if (isset($questions) && !empty($questions)): ?>
            <?php foreach ($questions as $question): ?>
                <div class="card question-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2 text-center">
                                <div class="vote-count"><?php echo isset($question['upvotes']) ? $question['upvotes'] : 0; ?></div>
                                <div class="small text-muted"><?php echo $text[$lang]['votes']; ?></div>
                                <div class="answer-count mt-2"><?php echo $question['answer_count']; ?></div>
                                <div class="small text-muted"><?php echo $text[$lang]['answers']; ?></div>
                            </div>
                            <div class="col-md-10">
                                <h5 class="card-title">
                                    <a href="index.php?page=view-question&id=<?php echo $question['id']; ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($question['title']); ?>
                                    </a>
                                </h5>
                                <p class="card-text"><?php echo substr(htmlspecialchars($question['content']), 0, 200); ?>...</p>
                                <div class="d-flex flex-wrap">
                                    <?php
                                    // Get tags for this question
                                    $query = "SELECT t.* FROM tags t 
                                              INNER JOIN question_tags qt ON t.id = qt.tag_id 
                                              WHERE qt.question_id = " . $question['id'];
                                    $tags = $db->select($query);
                                    
                                    if (!empty($tags)) {
                                        foreach ($tags as $tag) {
                                            echo '<span class="tag">' . htmlspecialchars($tag['name']) . '</span>';
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <small class="text-muted">
                                            <?php echo $text[$lang]['asked']; ?> 
                                            <?php echo date('d M Y', strtotime($question['created_at'])); ?>
                                        </small>
                                    </div>
                                    <div class="d-flex align-items-center"> 
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($question['username']); ?></div>
                                            <span class="reputation"><?php echo $question['reputation']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info"><?php echo $text[$lang]['no_questions']; ?></div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><?php echo $text[$lang]['popular_tags']; ?></h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap">
                    <?php
                    // Get popular tags
                    $query = "SELECT t.*, COUNT(qt.question_id) as question_count 
                              FROM tags t 
                              INNER JOIN question_tags qt ON t.id = qt.tag_id 
                              GROUP BY t.id 
                              ORDER BY question_count DESC 
                              LIMIT 10";
                    $popularTags = $db->select($query);
                    
                    if (!empty($popularTags)) {
                        foreach ($popularTags as $tag) {
                            echo '<a href="index.php?page=tag&id=' . $tag['id'] . '" class="tag text-decoration-none">' . 
                                    htmlspecialchars($tag['name']) . 
                                    ' <span class="badge bg-secondary rounded-pill">' . $tag['question_count'] . '</span>' . 
                                 '</a>';
                        }
                    } else {
                        echo '<p class="text-muted">' . $text[$lang]['no_tags'] . '</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><?php echo $text[$lang]['active_users']; ?></h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php
                    // Get active users
                    $query = "SELECT u.id, u.username, u.reputation 
                              FROM users u 
                              ORDER BY u.reputation DESC 
                              LIMIT 5";
                    $activeUsers = $db->select($query);
                    
                    if (!empty($activeUsers)) {
                        foreach ($activeUsers as $user) {
                            echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        
                                        <span>' . htmlspecialchars($user['username']) . '</span>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">' . $user['reputation'] . '</span>
                                  </li>';
                        }
                    } else {
                        echo '<li class="list-group-item">' . $text[$lang]['no_active_users'] . '</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Language switcher -->
<div class="language-switcher position-fixed" style="bottom: 20px; right: 20px;">
    <form action="index.php" method="post">
        <input type="hidden" name="set_language" value="<?php echo ($lang == 'ar') ? 'en' : 'ar'; ?>">
        <button type="submit" class="btn btn-sm btn-outline-secondary">
            <?php echo ($lang == 'ar') ? 'English' : 'العربية'; ?>
        </button>
    </form>
</div>

<script src="../assets/js/main.js"></script>
</body>
</html>