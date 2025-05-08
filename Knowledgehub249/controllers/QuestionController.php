
<?php

class QuestionController {
    private $db;
    private $userController;
    
    public function __construct($db) {
        $this->db = $db;
        $this->userController = new UserController($db);
    }
    
    public function getRecentQuestions($limit = 10) {
        $query = "SELECT q.*, u.username, u.reputation 
                  FROM questions q 
                  LEFT JOIN users u ON q.user_id = u.id 
                  ORDER BY q.created_at DESC 
                  LIMIT $limit";
        
        $questions = $this->db->select($query);
        
        // Add answer count and upvote information
        if (!empty($questions)) {
            foreach ($questions as $key => $question) {
                // Get answer count
                $query = "SELECT COUNT(*) as count FROM answers WHERE question_id = " . $question['id'];
                $answerCount = $this->db->select($query);
                $questions[$key]['answer_count'] = $answerCount[0]['count'];
                
                
// Get upvotes
$query = "SELECT COUNT(*) as count FROM votes WHERE post_id = " . $question['id'] . " AND post_type = 'question' AND vote_type = 1"; // Corrected columns
$upvotes = $this->db->select($query);
$questions[$key]['upvotes'] = $upvotes[0]['count'];

            }
        }
        
        return $questions;
    }
    
    public function getQuestionById($id) {
        $id = (int)$id;
        $query = "SELECT q.*, u.username, u.reputation 
                  FROM questions q 
                  LEFT JOIN users u ON q.user_id = u.id 
                  WHERE q.id = $id";
        
        $result = $this->db->select($query);
        
        if (!empty($result)) {
            return $result[0];
        }
        
        return null;
    }
    
    public function createQuestion($userId, $title, $content, $tags = []) {
        $userId = (int)$userId;
        $title = $this->db->connection->real_escape_string($title);
        $content = $this->db->connection->real_escape_string($content);
        
        $query = "INSERT INTO questions (user_id, title, content, created_at) 
                  VALUES ($userId, '$title', '$content', NOW())";
        
        $questionId = $this->db->insert($query);
        
        if ($questionId && !empty($tags)) {
            foreach ($tags as $tag) {
                $tag = trim($this->db->connection->real_escape_string($tag));
                
                // Check if tag exists
                $query = "SELECT id FROM tags WHERE name = '$tag'";
                $result = $this->db->select($query);
                
                if (empty($result)) {
                    // Create new tag
                    $query = "INSERT INTO tags (name) VALUES ('$tag')";
                    $tagId = $this->db->insert($query);
                } else {
                    $tagId = $result[0]['id'];
                }
                
                // Link tag to question
                $query = "INSERT INTO question_tags (question_id, tag_id) VALUES ($questionId, $tagId)";
                $this->db->insert($query);
            }
        }
        
        return $questionId;
    }
    
    public function updateQuestion($questionId, $title, $content, $tags = []) {
        $questionId = (int)$questionId;
        $title = $this->db->connection->real_escape_string($title);
        $content = $this->db->connection->real_escape_string($content);
        
        $query = "UPDATE questions SET title = '$title', content = '$content', updated_at = NOW() WHERE id = $questionId";
        $result = $this->db->update($query);
        
        if ($result && !empty($tags)) {
            // Remove existing tags
            $query = "DELETE FROM question_tags WHERE question_id = $questionId";
            $this->db->delete($query);
            
            // Add new tags
            foreach ($tags as $tag) {
                $tag = trim($this->db->connection->real_escape_string($tag));
                
                // Check if tag exists
                $query = "SELECT id FROM tags WHERE name = '$tag'";
                $result = $this->db->select($query);
                
                if (empty($result)) {
                    // Create new tag
                    $query = "INSERT INTO tags (name) VALUES ('$tag')";
                    $tagId = $this->db->insert($query);
                } else {
                    $tagId = $result[0]['id'];
                }
                
                // Link tag to question
                $query = "INSERT INTO question_tags (question_id, tag_id) VALUES ($questionId, $tagId)";
                $this->db->insert($query);
            }
        }
        
        return $result ? true : false;
    }
    
    public function deleteQuestion($id) {
        $id = (int)$id;
        // Delete question tags
        $query = "DELETE FROM question_tags WHERE question_id = $id";
        $this->db->delete($query);
        
        // Delete answers
        $query = "DELETE FROM answers WHERE question_id = $id";
        $this->db->delete($query);
        
 
     
        // Delete votes
        $query = "DELETE FROM votes WHERE post_id = $id AND post_type = 'question'"; // Corrected columns
        $this->db->delete($query);
        
        
        // Delete question
        $query = "DELETE FROM questions WHERE id = $id";
        return $this->db->delete($query) ? true : false;
    }
    
    public function searchQuestions($keyword) {
        $keyword = $this->db->connection->real_escape_string($keyword);
        
        $query = "SELECT q.*, u.username, u.reputation
                  FROM questions q 
                  LEFT JOIN users u ON q.user_id = u.id 
                  WHERE q.title LIKE '%$keyword%' OR q.content LIKE '%$keyword%' 
                  ORDER BY q.created_at DESC";
        
        return $this->db->select($query);
    }
    
    public function getQuestionsByTag($tagId) {
        $tagId = (int)$tagId;
        $query = "SELECT q.*, u.username, u.reputation
                  FROM questions q 
                  INNER JOIN question_tags qt ON q.id = qt.question_id 
                  LEFT JOIN users u ON q.user_id = u.id 
                  WHERE qt.tag_id = $tagId 
                  ORDER BY q.created_at DESC";
        
        return $this->db->select($query);
    }
    
    public function getQuestionsByUser($userId) {
        $userId = (int)$userId;
        $query = "SELECT * FROM questions WHERE user_id = $userId ORDER BY created_at DESC";
        return $this->db->select($query);
    }
    
    public function incrementViewCount($questionId) {
        $questionId = (int)$questionId;
        $query = "UPDATE questions SET view_count = view_count + 1 WHERE id = $questionId";
        return $this->db->update($query);
    }
    
    public function acceptAnswer($questionId, $answerId) {
        $questionId = (int)$questionId;
        $answerId = (int)$answerId;
        
        // Reset all answers for this question
        $query = "UPDATE answers SET is_accepted = 0 WHERE question_id = $questionId";
        $this->db->update($query);
        
        // Set the accepted answer
        $query = "UPDATE answers SET is_accepted = 1 WHERE id = $answerId AND question_id = $questionId";
        $result = $this->db->update($query);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'تم قبول الإجابة بنجاح'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'حدث خطأ أثناء قبول الإجابة'
            ];
        }
    }
    
    public function canUserEditQuestion($questionId, $userId) {
        // Admin can always edit
        if ($this->userController->getUserObject($userId)->isAdmin()) {
            return true;
        }
        
        // Check if user is the author
        $question = $this->getQuestionById($questionId);
        if ($question && $question['user_id'] == $userId) {
            return false;
        }
        
        // Check if user has silver badge or higher
        return $this->userController->canUserEditPosts($userId);
    }
}
?>