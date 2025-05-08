<?php


class SpammedPostController {
    private $db;
    public function __construct($db) { $this->db = $db; }

    public function reportSpam($postId, $postType, $reporterId, $reason) {
        $query = "SELECT * FROM spammed_posts WHERE post_id = $postId AND post_type = '$postType' AND reporter_id = $reporterId";
        if (!empty($this->db->select($query))) {
            return ['success' => false, 'message' => 'لقد قمت بالإبلاغ عن هذا المحتوى مسبقاً'];
        }
        $query = "INSERT INTO spammed_posts (post_id, post_type, reporter_id, reason, status, created_at) VALUES ($postId, '$postType', $reporterId, '$reason', 'pending', NOW())";
        return $this->db->insert($query) ? 
            ['success' => true, 'message' => 'تم الإبلاغ عن المحتوى بنجاح'] : 
            ['success' => false, 'message' => 'حدث خطأ أثناء الإبلاغ عن المحتوى'];
    }

    public function getAllSpammedPosts($status = null, $limit = 50, $offset = 0) {
        $statusFilter = $status ? "WHERE status = '$status'" : "";
        $query = "SELECT sp.*, 
                  CASE WHEN sp.post_type = 'question' THEN 
                    (SELECT title FROM questions WHERE id = sp.post_id)
                  WHEN sp.post_type = 'answer' THEN 
                    (SELECT content FROM answers WHERE id = sp.post_id LIMIT 50)
                  END as post_content,
                  u.username as reporter_username
                  FROM spammed_posts sp
                  LEFT JOIN users u ON sp.reporter_id = u.id
                  $statusFilter ORDER BY sp.created_at DESC LIMIT $limit OFFSET $offset";
        return $this->db->select($query);
    }

    public function getSpammedPostById($reportId) {
        $query = "SELECT sp.*, 
                  u.username as reporter_username,
                  CASE WHEN sp.post_type = 'question' THEN 
                    (SELECT user_id FROM questions WHERE id = sp.post_id)
                  WHEN sp.post_type = 'answer' THEN 
                    (SELECT user_id FROM answers WHERE id = sp.post_id)
                  END as post_author_id
                  FROM spammed_posts sp
                  LEFT JOIN users u ON sp.reporter_id = u.id
                  WHERE sp.id = $reportId";
        $result = $this->db->select($query);
        return $result[0] ?? null;
    }

    public function updateSpamStatus($reportId, $status) {
        $query = "UPDATE spammed_posts SET status = '$status', updated_at = NOW() WHERE id = $reportId";
        return $this->db->update($query);
    }

    public function deleteSpammedContent($reportId) {
        $report = $this->getSpammedPostById($reportId);
        if (!$report) return false;
        if ($report['post_type'] == 'question') {
            $this->db->delete("DELETE FROM questions WHERE id = " . $report['post_id']);
            $this->db->delete("DELETE FROM answers WHERE question_id = " . $report['post_id']);
        } else if ($report['post_type'] == 'answer') {
            $this->db->delete("DELETE FROM answers WHERE id = " . $report['post_id']);
        }
        $this->updateSpamStatus($reportId, 'approved');
        return true;
    }
}
?>
