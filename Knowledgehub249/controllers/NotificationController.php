<?php


class NotificationController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function createNotification($userId, $type, $message, $relatedId = null) {
        $query = "INSERT INTO notifications (user_id, type, message, related_id, is_read, created_at) 
                  VALUES ($userId, '$type', '$message', " . ($relatedId ? $relatedId : "NULL") . ", 0, NOW())";
        $notificationId = $this->db->insert($query);
        
        return $notificationId;
    }
    
    public function getUserNotifications($userId, $limit = 10, $offset = 0) {
        $query = "SELECT * FROM notifications 
                  WHERE user_id = $userId 
                  ORDER BY created_at DESC 
                  LIMIT $limit OFFSET $offset";
        $result = $this->db->select($query);
        return $result;
    }
    
    public function getUnreadNotificationsCount($userId) {
        $query = "SELECT COUNT(*) as count FROM notifications 
                  WHERE user_id = $userId AND is_read = 0";
        $result = $this->db->select($query);
        
        if (!empty($result)) {
            return $result[0]['count'];
        }
        
        return 0;
    }
    
    public function markAsRead($notificationId) {
        $query = "UPDATE notifications SET is_read = 1 WHERE id = $notificationId";
        $result = $this->db->update($query);
        return $result;
    }
    
    public function markAllAsRead($userId) {
        $query = "UPDATE notifications SET is_read = 1 WHERE user_id = $userId";
        $result = $this->db->update($query);
        return $result;
    }
    
    public function notifyOnUpvote($contentType, $contentId, $contentAuthorId) {
        $message = ($contentType == 'question') ? 
                   'تم التصويت بإيجابية على سؤالك' : 
                   'تم التصويت بإيجابية على إجابتك';
        
        return $this->createNotification(
            $contentAuthorId, 
            'upvote', 
            $message, 
            $contentId
        );
    }
    
    public function notifyOnAcceptedAnswer($answerId, $answerAuthorId, $questionId) {
        $message = 'تم قبول إجابتك كأفضل إجابة';
        
        return $this->createNotification(
            $answerAuthorId, 
            'accepted_answer', 
            $message, 
            $answerId
        );
    }
    
    public function notifyOnNewBadge($userId, $badgeName) {
        $message = 'مبروك! لقد حصلت على شارة جديدة: ' . $badgeName;
        
        return $this->createNotification(
            $userId, 
            'badge', 
            $message
        );
    }
    
    public function notifyOnContentEdit($contentType, $contentId, $contentAuthorId) {
        $message = ($contentType == 'question') ? 
                   'تم تعديل سؤالك بواسطة مستخدم آخر' : 
                   'تم تعديل إجابتك بواسطة مستخدم آخر';
        
        return $this->createNotification(
            $contentAuthorId, 
            'edit', 
            $message, 
            $contentId
        );
    }
    
    public function deleteNotificationsForContent($contentType, $contentId) {
        $query = "DELETE FROM notifications 
                  WHERE (type = '$contentType') AND related_id = $contentId";
        $result = $this->db->delete($query);
        return $result;
    }
}
?>
