<?php
class SpammedPost {
    private $id;
    private $postId;
    private $postType; // 'question' or 'answer'
    private $reporterId;
    private $reason;
    private $status; // 'pending', 'approved', 'rejected'
    private $createdAt;
    private $updatedAt;
    
    public function __construct($data = []) {
        $this->id = isset($data['id']) ? $data['id'] : null;
        $this->postId = isset($data['post_id']) ? $data['post_id'] : null;
        $this->postType = isset($data['post_type']) ? $data['post_type'] : '';
        $this->reporterId = isset($data['reporter_id']) ? $data['reporter_id'] : null;
        $this->reason = isset($data['reason']) ? $data['reason'] : '';
        $this->status = isset($data['status']) ? $data['status'] : 'pending';
        $this->createdAt = isset($data['created_at']) ? $data['created_at'] : null;
        $this->updatedAt = isset($data['updated_at']) ? $data['updated_at'] : null;
    }
    
    // Getters and setters
    public function getId() {
        return $this->id;
    }
    
    public function getPostId() {
        return $this->postId;
    }
    
    public function setPostId($postId) {
        $this->postId = $postId;
    }
    
    public function getPostType() {
        return $this->postType;
    }
    
    public function setPostType($postType) {
        $this->postType = $postType;
    }
    
    public function getReporterId() {
        return $this->reporterId;
    }
    
    public function setReporterId($reporterId) {
        $this->reporterId = $reporterId;
    }
    
    public function getReason() {
        return $this->reason;
    }
    
    public function setReason($reason) {
        $this->reason = $reason;
    }
    
    public function getStatus() {
        return $this->status;
    }
    
    public function setStatus($status) {
        $this->status = $status;
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
