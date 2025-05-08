

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

<?php
if (!isset($question) || empty($question)) {
    echo '<div class="alert alert-danger">السؤال غير موجود</div>';
    exit;
}
?>

<div class="row">
    <div class="col-md-9">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex">
                    <div class="vote-buttons text-center me-3">
                        <button class="upvote" onclick="voteQuestion(<?php echo $question['id']; ?>, 1)">
                            <i class="fas fa-caret-up"></i>
                        </button>
                        <div class="vote-count my-2"><?php echo isset($question['upvotes']) ? $question['upvotes'] : 0; ?></div>
                        <button class="downvote" onclick="voteQuestion(<?php echo $question['id']; ?>, -1)">
                            <i class="fas fa-caret-down"></i>
                        </button>
                    </div>
                    <div class="flex-grow-1">
                        <h3 class="card-title"><?php echo htmlspecialchars($question['title']); ?></h3>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($question['content'])); ?></p>
                        
                        <div class="d-flex flex-wrap mt-3">
                            <?php if (!empty($question['tags'])): ?>
                                <?php foreach ($question['tags'] as $tag): ?>
                                    <span class="tag"><?php echo htmlspecialchars($tag['name']); ?></span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" onclick="saveQuestion(<?php echo $question['id']; ?>)">
                                    <i class="far fa-bookmark me-1"></i> حفظ
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="dropdown">
                                    <i class="fas fa-share-alt me-1"></i> مشاركة
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="fab fa-facebook me-2"></i> فيسبوك</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fab fa-twitter me-2"></i> تويتر</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fab fa-whatsapp me-2"></i> واتساب</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-link me-2"></i> نسخ الرابط</a></li>
                                </ul>
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $question['user_id']): ?>
                                    <button class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-edit me-1"></i> تعديل
                                    </button>
                                <?php endif; ?>
                                <button class="btn btn-outline-danger btn-sm" onclick="reportQuestion(<?php echo $question['id']; ?>)">
                                    <i class="fas fa-flag me-1"></i> تبليغ
                                </button>
                            </div>
                            
                            <div class="text-end">
                                <div class="d-flex align-items-center">
                                    <img src="https://via.placeholder.com/32" class="rounded-circle me-2" alt="User Avatar">
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($question['username']); ?></div>
                                        <small class="text-muted">
                                            <i class="fas fa-star me-1 text-warning"></i>
                                            <span class="reputation"><?php echo $question['reputation']; ?></span>
                                        </small>
                                        <div class="small text-muted">سُئل <?php echo date('d M Y', strtotime($question['created_at'])); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

<?php if (isset($question['answers']) && is_array($question['answers'])): ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><?php echo count($question['answers']); ?> إجابة</h4>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                ترتيب حسب
            </button>
            <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                <li><a class="dropdown-item active" href="#">الأكثر تصويتاً</a></li>
                <li><a class="dropdown-item" href="#">الأحدث</a></li>
                <li><a class="dropdown-item" href="#">الأقدم</a></li>
            </ul>
        </div>
    </div>

    <?php foreach ($question['answers'] as $answer): ?>
        <div class="card mb-3 <?php echo $answer['is_accepted'] ? 'border-success' : ''; ?>">
            <?php if ($answer['is_accepted']): ?>
                <div class="card-header bg-success text-white">
                    <i class="fas fa-check-circle me-1"></i> الإجابة المقبولة
                </div>
            <?php endif; ?>
            <div class="card-body">
                <div class="d-flex">
                    <div class="vote-buttons text-center me-3">
                        <button class="upvote" onclick="voteAnswer(<?php echo $answer['id']; ?>, 1)">
                            <i class="fas fa-caret-up"></i>
                        </button>
                        <div class="vote-count my-2"><?php echo $answer['upvotes'] - $answer['downvotes']; ?></div>
                        <button class="downvote" onclick="voteAnswer(<?php echo $answer['id']; ?>, -1)">
                            <i class="fas fa-caret-down"></i>
                        </button>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $question['user_id'] && !$answer['is_accepted']): ?>
                            <button class="mt-2 accept-answer" onclick="acceptAnswer(<?php echo $answer['id']; ?>)" title="قبول الإجابة">
                                <i class="far fa-check-circle"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="flex-grow-1">
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($answer['content'])); ?></p>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="btn-group">
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $answer['user_id']): ?>
                                    <button class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-edit me-1"></i> تعديل
                                    </button>
                                <?php endif; ?>
                                <button class="btn btn-outline-danger btn-sm" onclick="reportAnswer(<?php echo $answer['id']; ?>)">
                                    <i class="fas fa-flag me-1"></i> تبليغ
                                </button>
                            </div>

                            <div class="text-end">
                                <div class="d-flex align-items-center">
                                    <img src="https://via.placeholder.com/32" class="rounded-circle me-2" alt="User Avatar">
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($answer['username']); ?></div>
                                        <small class="text-muted">
                                            <i class="fas fa-star me-1 text-warning"></i>
                                            <span class="reputation"><?php echo $answer['reputation']; ?></span>
                                        </small>
                                        <div class="small text-muted">أجاب <?php echo date('d M Y', strtotime($answer['created_at'])); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-info">لا توجد إجابات على هذا السؤال بعد. كن أول من يجيب!</div>
<?php endif; ?>

        
       
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">إجابتك</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="index.php?page=submit-answer">
                        <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                        <div class="mb-3">
                            <textarea class="form-control" id="answer_content" name="content" rows="5" placeholder="اكتب إجابتك هنا..." required></textarea>
                        </div>
                        <button type="submit" name="submit_answer" class="btn btn-primary">نشر الإجابة</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-secondary mt-4">
                <a href="index.php?page=login">سجل دخول</a> أو <a href="index.php?page=register">أنشئ حساباً</a> للإجابة على هذا السؤال.
            </div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">أسئلة ذات صلة</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php
                    // Get related questions based on tags
                   

// Get related questions based on tags
$tagIds = []; // Initialize $tagIds as an empty array
if (isset($question['tags']) && is_array($question['tags'])) {
    $tagIds = array_map(function($tag) {
        return $tag['id'];
    }, $question['tags']);
}

if (!empty($tagIds)) {
    $tagIdsStr = implode(',', $tagIds);

    $query = "SELECT DISTINCT q.id, q.title,
              COUNT(a.id) as answer_count
              FROM questions q
              LEFT JOIN answers a ON q.id = a.question_id
              INNER JOIN question_tags qt ON q.id = qt.question_id
              WHERE q.id != " . $question['id'] . "
              AND qt.tag_id IN ($tagIdsStr)
              GROUP BY q.id
              ORDER BY COUNT(DISTINCT qt.tag_id) DESC, q.created_at DESC
              LIMIT 5";

    $relatedQuestions = $db->select($query);

    if (!empty($relatedQuestions)) {
        foreach ($relatedQuestions as $relatedQuestion) {
            echo '<li class="list-group-item">
                    <a href="index.php?page=view-question&id=' . $relatedQuestion['id'] . '" class="text-decoration-none">
                        ' . htmlspecialchars($relatedQuestion['title']) . '
                    </a>
                    <span class="badge bg-secondary float-end">' . $relatedQuestion['answer_count'] . ' إجابة</span>
                  </li>';
        }
    } else {
        echo '<li class="list-group-item">لا توجد أسئلة ذات صلة</li>';
    }
} 



                    if (!empty($tagIds)) {
                        $tagIdsStr = implode(',', $tagIds);
                        
                        $query = "SELECT DISTINCT q.id, q.title, 
                                  COUNT(a.id) as answer_count 
                                  FROM questions q 
                                  LEFT JOIN answers a ON q.id = a.question_id 
                                  INNER JOIN question_tags qt ON q.id = qt.question_id 
                                  WHERE q.id != " . $question['id'] . " 
                                  AND qt.tag_id IN ($tagIdsStr) 
                                  GROUP BY q.id 
                                  ORDER BY COUNT(DISTINCT qt.tag_id) DESC, q.created_at DESC 
                                  LIMIT 5";
                        
                        $relatedQuestions = $db->select($query);
                        
                        if (!empty($relatedQuestions)) {
                            foreach ($relatedQuestions as $relatedQuestion) {
                                echo '<li class="list-group-item">
                                        <a href="index.php?page=view-question&id=' . $relatedQuestion['id'] . '" class="text-decoration-none">
                                            ' . htmlspecialchars($relatedQuestion['title']) . '
                                        </a>
                                        <span class="badge bg-secondary float-end">' . $relatedQuestion['answer_count'] . ' إجابة</span>
                                      </li>';
                            }
                        } else {
                            echo '<li class="list-group-item">لا توجد أسئلة ذات صلة</li>';
                        }
                    } else {
                        echo '<li class="list-group-item">لا توجد أسئلة ذات صلة</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">الأسئلة الأكثر تصويتاً</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php
                    // Get hot questions
                    $query = "SELECT q.id, q.title, 
                              (SELECT COUNT(*) FROM votes WHERE question_id = q.id AND vote_type = 1) as upvotes 
                              FROM questions q 
                              ORDER BY upvotes DESC 
                              LIMIT 5";
                    
                 
                    // Get hot questions
                    $query = "SELECT q.id, q.title,
                              (SELECT COUNT(*) FROM votes WHERE post_type = 'question' AND post_id = q.id AND vote_type = 1) as upvotes
                              FROM questions q
                              ORDER BY upvotes DESC
                              LIMIT 5";
                    
                    $hotQuestions = $db->select($query);
                    
                    if (!empty($hotQuestions)) {
                        foreach ($hotQuestions as $hotQuestion) {
                            echo '<li class="list-group-item">
                                    <a href="index.php?page=view-question&id=' . $hotQuestion['id'] . '" class="text-decoration-none">
                                        ' . htmlspecialchars($hotQuestion['title']) . '
                                    </a>
                                    <span class="badge bg-danger float-end">' . $hotQuestion['upvotes'] . ' تصويت</span>
                                  </li>';
                        }
                    } else {
                        echo '<li class="list-group-item">لا توجد أسئلة مميزة بعد</li>';
                    }
                    ?>
                    
                  
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    function voteQuestion(questionId, voteType) {
        <?php if (!isset($_SESSION['user_id'])): ?>
            alert('يجب تسجيل الدخول للتصويت');
            window.location.href = 'index.php?page=login';
            return;
        <?php endif; ?>
        
        // Here you would normally make an AJAX request to vote
        alert(voteType === 1 ? 'تم التصويت إيجاباً للسؤال' : 'تم التصويت سلباً للسؤال');
    }
    
    function voteAnswer(answerId, voteType) {
        <?php if (!isset($_SESSION['user_id'])): ?>
            alert('يجب تسجيل الدخول للتصويت');
            window.location.href = 'index.php?page=login';
            return;
        <?php endif; ?>
        
        // Here you would normally make an AJAX request to vote
        alert(voteType === 1 ? 'تم التصويت إيجاباً للإجابة' : 'تم التصويت سلباً للإجابة');
    }
    
    function acceptAnswer(answerId) {
        <?php if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $question['user_id']): ?>
            alert('فقط صاحب السؤال يمكنه قبول الإجابة');
            return;
        <?php endif; ?>
        
        // Here you would normally make an AJAX request to accept the answer
        alert('تم قبول الإجابة');
    }
    
    function saveQuestion(questionId) {
        <?php if (!isset($_SESSION['user_id'])): ?>
            alert('يجب تسجيل الدخول لحفظ السؤال');
            window.location.href = 'index.php?page=login';
            return;
        <?php endif; ?>
        
        // Here you would normally make an AJAX request to save the question
        alert('تم حفظ السؤال');
    }
    
    function reportQuestion(questionId) {
        <?php if (!isset($_SESSION['user_id'])): ?>
            alert('يجب تسجيل الدخول للتبليغ عن السؤال');
            window.location.href = 'index.php?page=login';
            return;
        <?php endif; ?>
        
        // Here you would normally show a modal to report the question
        alert('تم التبليغ عن السؤال');
    }
    
    function reportAnswer(answerId) {
        <?php if (!isset($_SESSION['user_id'])): ?>
            alert('يجب تسجيل الدخول للتبليغ عن الإجابة');
            window.location.href = 'index.php?page=login';
            return;
        <?php endif; ?>
        
        // Here you would normally show a modal to report the answer
        alert('تم التبليغ عن الإجابة');
    }
</script>


