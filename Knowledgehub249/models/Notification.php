<?php
class Notification {
    private $id;
    private $userId;
    private $type; // 'upvote', 'accepted_answer', 'badge', 'edit'
    private $message;
    private $relatedId; // ID of question, answer, etc.
    private $isRead;
    private $createdAt;
    
    public function __construct($data = []) {
        $this->id = isset($data['id']) ? $data['id'] : null;
        $this->userId = isset($data['user_id']) ? $data['user_id'] : null;
        $this->type = isset($data['type']) ? $data['type'] : '';
        $this->message = isset($data['message']) ? $data['message'] : '';
        $this->relatedId = isset($data['related_id']) ? $data['related_id'] : null;
        $this->isRead = isset($data['is_read']) ? $data['is_read'] : 0;
        $this->createdAt = isset($data['created_at']) ? $data['created_at'] : null;
    }
    
    // Getters and setters
    public function getId() {
        return $this->id;
    }
    
    public function getUserId() {
        return $this->userId;
    }
    
    public function setUserId($userId) {
        $this->userId = $userId;
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function setType($type) {
        $this->type = $type;
    }
    
    public function getMessage() {
        return $this->message;
    }
    
    public function setMessage($message) {
        $this->message = $message;
    }
    
    public function getRelatedId() {
        return $this->relatedId;
    }
    
    public function setRelatedId($relatedId) {
        $this->relatedId = $relatedId;
    }
    
    public function isRead() {
        return $this->isRead == 1;
    }
    
    public function setIsRead($isRead) {
        $this->isRead = $isRead ? 1 : 0;
    }
    
    public function getCreatedAt() {
        return $this->createdAt;
    }
}
?>
