<?php
class Answer {
    private $id;
    private $questionId;
    private $userId;
    private $content;
    private $isAccepted;
    private $createdAt;
    private $updatedAt;
    
    public function __construct($data = []) {
        $this->id = isset($data['id']) ? $data['id'] : null;
        $this->questionId = isset($data['question_id']) ? $data['question_id'] : null;
        $this->userId = isset($data['user_id']) ? $data['user_id'] : null;
        $this->content = isset($data['content']) ? $data['content'] : '';
        $this->isAccepted = isset($data['is_accepted']) ? $data['is_accepted'] : 0;
        $this->createdAt = isset($data['created_at']) ? $data['created_at'] : '';
        $this->updatedAt = isset($data['updated_at']) ? $data['updated_at'] : null;
    }
    
    // Getters and setters
    public function getId() {
        return $this->id;
    }
    
    public function getQuestionId() {
        return $this->questionId;
    }
    
    public function getUserId() {
        return $this->userId;
    }
    
    public function getContent() {
        return $this->content;
    }
    
    public function setContent($content) {
        $this->content = $content;
    }
    
    public function isAccepted() {
        return $this->isAccepted == 1;
    }
    
    public function setIsAccepted($isAccepted) {
        $this->isAccepted = $isAccepted ? 1 : 0;
    }
    
    public function getCreatedAt() {
        return $this->createdAt;
    }
    
    public function getUpdatedAt() {
        return $this->updatedAt;
    }
    
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
    }
}
?>
