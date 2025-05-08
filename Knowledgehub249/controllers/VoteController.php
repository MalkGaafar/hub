<?php

class VoteController {
    private $db;
    private $table = "votes";
    private $userController;
    
    // Vote properties - matching database column names
    public $id;
    public $user_id;
    public $post_type; // 'question' or 'answer'
    public $post_id;
    public $vote_type; // 1 for upvote, -1 for downvote
    public $created_at;
    
    public function __construct($db) {
        $this->db = $db;
        $this->userController = new UserController($db);
    }
    
    /**
     * Create, update or remove a vote
     * 
     * @param int $post_id The ID of the question or answer
     * @param string $post_type Either 'question' or 'answer'
     * @param int $user_id The ID of the voting user
     * @param string $vote_direction Either 'up' or 'down'
     * @return array Result with success status and message
     */
    public function vote($post_id, $post_type, $user_id, $vote_direction) {
        // Sanitize inputs
        $post_id = (int)$post_id;
        $user_id = (int)$user_id;
        $post_type = $this->db->connection->real_escape_string($post_type);
        
        // Validate vote direction
        if ($vote_direction !== 'up' && $vote_direction !== 'down') {
            return [
                'success' => false,
                'message' => 'نوع التصويت غير صالح'
            ];
        }
        
        // Validate post type
        if ($post_type !== 'question' && $post_type !== 'answer') {
            return [
                'success' => false,
                'message' => 'نوع المحتوى غير صالح'
            ];
        }
        
        // Check if content exists and get author
        $table = $post_type === 'question' ? 'questions' : 'answers';
        $query = "SELECT user_id FROM $table WHERE id = $post_id";
        $result = $this->db->select($query);
        
        if (empty($result)) {
            return [
                'success' => false,
                'message' => 'المحتوى غير موجود'
            ];
        }
        
        // Prevent self-voting
        $author_id = $result[0]['user_id'];
        if ($author_id == $user_id) {
            return [
                'success' => false,
                'message' => 'لا يمكنك التصويت على المحتوى الخاص بك'
            ];
        }
        
        // Convert vote direction to numeric value
        $vote_type = ($vote_direction === 'up') ? 1 : -1;
        
        // Check if user already voted
        $query = "SELECT * FROM $this->table 
                 WHERE post_id = $post_id 
                 AND post_type = '$post_type' 
                 AND user_id = $user_id";
        $existingVote = $this->db->select($query);
        
        if (empty($existingVote)) {
            // Insert new vote
            $query = "INSERT INTO $this->table (post_id, post_type, user_id, vote_type, created_at) 
                     VALUES ($post_id, '$post_type', $user_id, $vote_type, NOW())";
            $result = $this->db->insert($query);
            
            if ($result) {
                $this->updateContentScore($post_id, $post_type, $vote_type);
                $this->updateUserReputation($post_id, $post_type, $vote_type);
                
                return [
                    'success' => true,
                    'message' => ($vote_direction === 'up') ? 'تم التصويت الإيجابي بنجاح' : 'تم التصويت السلبي بنجاح',
                    'vote_status' => $vote_type
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'حدث خطأ أثناء التصويت'
                ];
            }
        } else {
            $existingVoteValue = $existingVote[0]['vote_type'];
            
            // If user is voting the same way, remove the vote
            if ($existingVoteValue == $vote_type) {
                $query = "DELETE FROM $this->table 
                         WHERE post_id = $post_id 
                         AND post_type = '$post_type' 
                         AND user_id = $user_id";
                $result = $this->db->delete($query);
                
                if ($result) {
                    $this->updateContentScore($post_id, $post_type, -$vote_type);
                    $this->updateUserReputation($post_id, $post_type, -$vote_type);
                    
                    return [
                        'success' => true,
                        'message' => 'تم إلغاء التصويت',
                        'vote_status' => 0
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'حدث خطأ أثناء إلغاء التصويت'
                    ];
                }
            } else {
                // If user is changing vote, update it
                $query = "UPDATE $this->table 
                         SET vote_type = $vote_type 
                         WHERE post_id = $post_id 
                         AND post_type = '$post_type' 
                         AND user_id = $user_id";
                $result = $this->db->update($query);
                
                if ($result) {
                    // Double the effect since we're switching from -1 to 1 or vice versa
                    $this->updateContentScore($post_id, $post_type, 2 * $vote_type);
                    $this->updateUserReputation($post_id, $post_type, 2 * $vote_type);
                    
                    return [
                        'success' => true,
                        'message' => ($vote_direction === 'up') ? 'تم تغيير التصويت إلى إيجابي' : 'تم تغيير التصويت إلى سلبي',
                        'vote_status' => $vote_type
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'حدث خطأ أثناء تغيير التصويت'
                    ];
                }
            }
        }
    }
    
    /**
     * Update the score of the question or answer
     */
    private function updateContentScore($post_id, $post_type, $vote_type) {
        $post_id = (int)$post_id;
        $vote_type = (int)$vote_type;
        $table = $post_type === 'question' ? 'questions' : 'answers';
        
        $query = "UPDATE $table SET score = score + $vote_type WHERE id = $post_id";
        $this->db->update($query);
    }
    
    /**
     * Update the reputation of the content author
     */
    private function updateUserReputation($post_id, $post_type, $vote_type) {
        $post_id = (int)$post_id;
        $vote_type = (int)$vote_type;
        
        // Get author ID
        $table = $post_type === 'question' ? 'questions' : 'answers';
        $query = "SELECT user_id FROM $table WHERE id = $post_id";
        $result = $this->db->select($query);
        
        if (!empty($result)) {
            $author_id = $result[0]['user_id'];
            
            // Adjust reputation value based on content type
            $reputationChange = $post_type === 'question' ? ($vote_type * 5) : ($vote_type * 10);
            
            // Update user reputation
            $query = "UPDATE users SET reputation = reputation + $reputationChange WHERE id = $author_id";
            $this->db->update($query);
            
            // Check if user reached a badge threshold and notify them
            if ($vote_type > 0) {
                $userRep = $this->userController->getUserReputation($author_id);
                
                if ($userRep == Badge::SILVER_THRESHOLD || 
                    $userRep == Badge::BRONZE_THRESHOLD || 
                    $userRep == Badge::GOLD_THRESHOLD) {
                    // In a real app, you would send a notification here
                }
            }
        }
    }
    
    /**
     * Get the user's vote status for a piece of content
     * 
     * @return int 1 for upvote, -1 for downvote, 0 for no vote
     */
    public function getUserVote($post_id, $post_type, $user_id) {
        if (!$user_id) {
            return 0;
        }
        
        $post_id = (int)$post_id;
        $user_id = (int)$user_id;
        $post_type = $this->db->connection->real_escape_string($post_type);
        
        $query = "SELECT vote_type FROM $this->table 
                 WHERE post_id = $post_id 
                 AND post_type = '$post_type' 
                 AND user_id = $user_id";
        $result = $this->db->select($query);
        
        if (!empty($result)) {
            return (int)$result[0]['vote_type'];
        }
        
        return 0;
    }
    
    /**
     * Get the upvote and downvote counts for a piece of content
     */
    public function getVoteCounts($post_id, $post_type) {
        $post_id = (int)$post_id;
        $post_type = $this->db->connection->real_escape_string($post_type);
        
        $query = "SELECT 
                 SUM(CASE WHEN vote_type = 1 THEN 1 ELSE 0 END) as upvotes,
                 SUM(CASE WHEN vote_type = -1 THEN 1 ELSE 0 END) as downvotes
                 FROM $this->table 
                 WHERE post_id = $post_id AND post_type = '$post_type'";
        $result = $this->db->select($query);
        
        if (!empty($result)) {
            return [
                'upvotes' => (int)$result[0]['upvotes'] ?: 0,
                'downvotes' => (int)$result[0]['downvotes'] ?: 0
            ];
        }
        
        return [
            'upvotes' => 0,
            'downvotes' => 0
        ];
    }
}
?>