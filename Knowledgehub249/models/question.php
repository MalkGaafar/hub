
<?php
class Question {
    private $id;
    private $userId;
    private $title;
    private $content;
    private $viewCount;
    private $upvotes;
    private $downvotes;
    private $isSolved;
    private $acceptedAnswerId;
    private $createdAt;
    private $updatedAt;
    
    public function __construct($data = []) {
        $this->id = isset($data['id']) ? $data['id'] : null;
        $this->userId = isset($data['user_id']) ? $data['user_id'] : null;
        $this->title = isset($data['title']) ? $data['title'] : '';
        $this->content = isset($data['content']) ? $data['content'] : '';
        $this->viewCount = isset($data['view_count']) ? $data['view_count'] : 0;
        $this->upvotes = isset($data['upvotes']) ? $data['upvotes'] : 0;
        $this->downvotes = isset($data['downvotes']) ? $data['downvotes'] : 0;
        $this->isSolved = isset($data['is_solved']) ? $data['is_solved'] : 0;
        $this->acceptedAnswerId = isset($data['accepted_answer_id']) ? $data['accepted_answer_id'] : null;
        $this->createdAt = isset($data['created_at']) ? $data['created_at'] : '';
        $this->updatedAt = isset($data['updated_at']) ? $data['updated_at'] : null;
    }
    
    // Getters and setters
    public function getId() {
        return $this->id;
    }
    
    public function getUserId() {
        return $this->userId;
    }
    
    public function getTitle() {
        return $this->title;
    }
    
    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function getContent() {
        return $this->content;
    }
    
    public function setContent($content) {
        $this->content = $content;
    }
    
    public function getViewCount() {
        return $this->viewCount;
    }
    
    public function incrementViewCount() {
        $this->viewCount++;
    }
    
    public function getUpvotes() {
        return $this->upvotes;
    }
    
    public function incrementUpvotes() {
        $this->upvotes++;
    }
    
    public function getDownvotes() {
        return $this->downvotes;
    }
    
    public function incrementDownvotes() {
        $this->downvotes++;
    }
    
    public function isSolved() {
        return $this->isSolved == 1;
    }
    
    public function setSolved($isSolved) {
        $this->isSolved = $isSolved ? 1 : 0;
    }
    
    public function getAcceptedAnswerId() {
        return $this->acceptedAnswerId;
    }
    
    public function setAcceptedAnswerId($answerId) {
        $this->acceptedAnswerId = $answerId;
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
    
    public function getNetVotes() {
        return $this->upvotes - $this->downvotes;
    }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'title' => $this->title,
            'content' => $this->content,
            'view_count' => $this->viewCount,
            'upvotes' => $this->upvotes,
            'downvotes' => $this->downvotes,
            'is_solved' => $this->isSolved,
            'accepted_answer_id' => $this->acceptedAnswerId,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}
?>
