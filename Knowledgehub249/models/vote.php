<?php
class Vote {
    private $db;
    private $table = "votes";
    
    // Vote properties
    public $id;
    public $user_id;
    public $post_type; // 'question' or 'answer'
    public $post_id;
    public $vote_type; // 1 for upvote, -1 for downvote
    public $created_at;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    // Create or update a vote
    public function vote($user_id, $post_type, $post_id, $vote_type) {
        // Check if user already voted on this post
        $checkQuery = "SELECT * FROM " . $this->table . " 
                    WHERE user_id = '$user_id' AND post_type = '$post_type' AND post_id = '$post_id'";
        
        $result = $this->db->select($checkQuery);
        
        if ($result) {
            // User already voted, update the vote
            $existing_vote = $result[0];
            
            // If user tries to vote the same way again, remove the vote
            if ($existing_vote['vote_type'] == $vote_type) {
                $deleteQuery = "DELETE FROM " . $this->table . " 
                            WHERE user_id = '$user_id' AND post_type = '$post_type' AND post_id = '$post_id'";
                
                $this->db->delete($deleteQuery);
                return 0; // Vote removed
            } else {
                // Change the vote
                $updateQuery = "UPDATE " . $this->table . " 
                            SET vote_type = '$vote_type' 
                            WHERE user_id = '$user_id' AND post_type = '$post_type' AND post_id = '$post_id'";
                
                $this->db->update($updateQuery);
                return $vote_type; // Vote changed
            }
        } else {
            // New vote
            $insertQuery = "INSERT INTO " . $this->table . " 
                        (user_id, post_type, post_id, vote_type, created_at) 
                        VALUES ('$user_id', '$post_type', '$post_id', '$vote_type', NOW())";
            
            $this->db->insert($insertQuery);
            return $vote_type; // New vote added
        }
    }
    
    // Get vote counts for a post
    public function getVoteCounts($post_type, $post_id) {
        $query = "SELECT 
                SUM(CASE WHEN vote_type = 1 THEN 1 ELSE 0 END) as upvotes,
                SUM(CASE WHEN vote_type = -1 THEN 1 ELSE 0 END) as downvotes
                FROM " . $this->table . " 
                WHERE post_type = '$post_type' AND post_id = '$post_id'";
        
        $result = $this->db->select($query);
        
        if ($result) {
            return [
                'upvotes' => (int)$result[0]['upvotes'] ?: 0,
                'downvotes' => (int)$result[0]['downvotes'] ?: 0
            ];
        }
        
        return ['upvotes' => 0, 'downvotes' => 0];
    }
    
    // Check if a user has voted on a post
    public function getUserVote($user_id, $post_type, $post_id) {
        $query = "SELECT vote_type FROM " . $this->table . " 
                WHERE user_id = '$user_id' AND post_type = '$post_type' AND post_id = '$post_id'";
        
        $result = $this->db->select($query);
        
        if ($result) {
            return (int)$result[0]['vote_type'];
        }
        
        return 0; // No vote
    }
}
?>
