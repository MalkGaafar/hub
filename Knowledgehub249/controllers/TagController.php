
<?php


class TagController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getAllTags() {
        $query = "SELECT * FROM tags ORDER BY name ASC";
        $result = $this->db->select($query);
        return $result;
    }
    
    public function getPopularTags($limit = 10) {
        $query = "SELECT t.*, COUNT(qt.question_id) as question_count 
                  FROM tags t 
                  LEFT JOIN question_tags qt ON t.id = qt.tag_id 
                  GROUP BY t.id 
                  ORDER BY question_count DESC 
                  LIMIT $limit";
        $result = $this->db->select($query);
        return $result;
    }
    
    public function getTagById($tagId) {
        $query = "SELECT * FROM tags WHERE id = $tagId";
        $result = $this->db->select($query);
        
        if (!empty($result)) {
            return $result[0];
        }
        
        return null;
    }
    
    public function createTag($name, $description = '') {
        // Check if tag already exists
        $query = "SELECT * FROM tags WHERE name = '$name'";
        $result = $this->db->select($query);
        
        if (!empty($result)) {
            return $result[0]['id']; // Return existing tag id
        }
        
        // Create new tag
        $query = "INSERT INTO tags (name, description, created_at) 
                  VALUES ('$name', '$description', NOW())";
        $tagId = $this->db->insert($query);
        
        return $tagId;
    }
    
    public function assignTagsToQuestion($questionId, $tags) {
        // Delete existing tags for this question
        $query = "DELETE FROM question_tags WHERE question_id = $questionId";
        $this->db->delete($query);
        
        // Add new tags
        foreach ($tags as $tagName) {
            $tagName = trim($tagName);
            if (empty($tagName)) continue;
            
            $tagId = $this->createTag($tagName);
            
            if ($tagId) {
                $query = "INSERT INTO question_tags (question_id, tag_id, created_at) 
                          VALUES ($questionId, $tagId, NOW())";
                $this->db->insert($query);
            }
        }
        
        return true;
    }
    
    public function getQuestionsByTag($tagId, $limit = 10, $offset = 0) {
        $query = "SELECT q.* 
                  FROM questions q 
                  JOIN question_tags qt ON q.id = qt.question_id 
                  WHERE qt.tag_id = $tagId 
                  ORDER BY q.created_at DESC 
                  LIMIT $limit OFFSET $offset";
        $result = $this->db->select($query);
        return $result;
    }
    
    public function getTagsForQuestion($questionId) {
        $query = "SELECT t.* 
                  FROM tags t 
                  JOIN question_tags qt ON t.id = qt.tag_id 
                  WHERE qt.question_id = $questionId";
        $result = $this->db->select($query);
        return $result;
    }
}
?>

