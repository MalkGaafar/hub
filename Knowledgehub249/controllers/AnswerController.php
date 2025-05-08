

<?php

require_once 'DBController.php';

class AnswerController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function createAnswer($userId, $questionId, $content) {
        $content = $this->db->connection->real_escape_string($content);
        $query = "INSERT INTO answers (user_id, question_id, content, created_at) 
                  VALUES ($userId, $questionId, '$content', NOW())";
        $answerId = $this->db->insert($query);
        
        if ($answerId) {
            // Update question's answer count
            $this->updateAnswerCount($questionId);
            
            return [
                'success' => true,
                'answer_id' => $answerId
            ];
        } else {
            return [
                'success' => false,
                'message' => 'فشل في إنشاء الإجابة'
            ];
        }
    }
    
    public function getAnswersByQuestionId($questionId) {
        $query = "SELECT a.*, u.username, u.reputation
                  FROM answers a
                  JOIN users u ON a.user_id = u.id
                  WHERE a.question_id = $questionId
                  ORDER BY a.is_accepted DESC, a.created_at ASC";
        $answers = $this->db->select($query);
        
        return $answers;
    }
    
    public function getAnswerById($answerId) {
        $query = "SELECT a.*, u.username, u.reputation
                  FROM answers a
                  JOIN users u ON a.user_id = u.id
                  WHERE a.id = $answerId";
        $result = $this->db->select($query);
        
        if (!empty($result)) {
            return $result[0];
        }
        
        return null;
    }
    
    public function updateAnswer($answerId, $content) {
        $content = $this->db->connection->real_escape_string($content);
        $query = "UPDATE answers SET content = '$content', updated_at = NOW() WHERE id = $answerId";
        $result = $this->db->update($query);
        
        return $result ? true : false;
    }
    
    public function deleteAnswer($answerId) {
        // First get the question ID to update answer count later
        $query = "SELECT question_id FROM answers WHERE id = $answerId";
        $result = $this->db->select($query);
        $questionId = null;
        
        if (!empty($result)) {
            $questionId = $result[0]['question_id'];
        }
        
        $query = "DELETE FROM answers WHERE id = $answerId";
        $result = $this->db->delete($query);
        
        if ($result && $questionId) {
            $this->updateAnswerCount($questionId);
        }
        
        return $result ? true : false;
    }
    
    public function acceptAnswer($answerId) {
        // Get question ID first
        $query = "SELECT question_id FROM answers WHERE id = $answerId";
        $result = $this->db->select($query);
        
        if (empty($result)) {
            return false;
        }
        
        $questionId = $result[0]['question_id'];
        
        // Remove accepted status from all answers for this question
        $query = "UPDATE answers SET is_accepted = 0 WHERE question_id = $questionId";
        $this->db->update($query);
        
        // Set this answer as accepted
        $query = "UPDATE answers SET is_accepted = 1 WHERE id = $answerId";
        $result = $this->db->update($query);
        
        return $result ? true : false;
    }
    
    private function updateAnswerCount($questionId) {
        $query = "SELECT COUNT(*) as total FROM answers WHERE question_id = $questionId";
        $result = $this->db->select($query);
        $count = 0;
        
        if (!empty($result)) {
            $count = $result[0]['total'];
        }
        
        $query = "UPDATE questions SET answer_count = $count WHERE id = $questionId";
        $this->db->update($query);
    }
    
    public function getUserAnswersCount($userId) {
        $query = "SELECT COUNT(*) as total FROM answers WHERE user_id = $userId";
        $result = $this->db->select($query);
        
        if (!empty($result)) {
            return $result[0]['total'];
        }
        
        return 0;
    }
}
?>
