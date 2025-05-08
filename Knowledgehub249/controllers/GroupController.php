<?php


class GroupController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    // This function should only be accessible by admins
    public function createGroup($name, $description, $creatorId) {
        // Check if the creator is admin
        $query = "SELECT is_admin FROM users WHERE id = $creatorId";
        $result = $this->db->select($query);
        
        if (empty($result) || $result[0]['is_admin'] != 1) {
            return [
                'success' => false,
                'message' => 'فقط المسؤولين يمكنهم إنشاء مجموعات جديدة'
            ];
        }
        
        // Check if group name already exists
        $name = $this->db->connection->real_escape_string($name); // Escape group name
        $query = "SELECT * FROM groups WHERE name = '$name'";
        $result = $this->db->select($query);
        
        if (!empty($result)) {
            return [
                'success' => false,
                'message' => 'اسم المجموعة موجود بالفعل'
            ];
        }
        
        // Escape group description
        $description = $this->db->connection->real_escape_string($description);
        
        // Create new group
        $query = "INSERT INTO groups (name, description, creator_id, created_at) 
                  VALUES ('$name', '$description', $creatorId, NOW())";
        $groupId = $this->db->insert($query);
        
        if ($groupId) {
            // Add creator as member and admin
            $query = "INSERT INTO group_members (group_id, user_id, is_admin, joined_at) 
                      VALUES ($groupId, $creatorId, 1, NOW())";
            $this->db->insert($query);
            
            return [
                'success' => true,
                'message' => 'تم إنشاء المجموعة بنجاح',
                'groupId' => $groupId
            ];
        } else {
            return [
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء المجموعة'
            ];
        }
    }
    
    public function getAllGroups() {
        $query = "SELECT g.*, 
                  (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as member_count,
                  u.username as creator_name
                  FROM groups g
                  LEFT JOIN users u ON g.creator_id = u.id
                  ORDER BY g.created_at DESC";
        $result = $this->db->select($query);
        return $result;
    }
    
    public function getGroupById($groupId) {
        $query = "SELECT g.*, 
                  (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as member_count,
                  u.username as creator_name
                  FROM groups g
                  LEFT JOIN users u ON g.creator_id = u.id
                  WHERE g.id = $groupId";
        $result = $this->db->select($query);
        
        if (!empty($result)) {
            return $result[0];
        }
        
        return null;
    }
    
    public function joinGroup($groupId, $userId) {
        // Check if user is already a member
        $query = "SELECT * FROM group_members WHERE group_id = $groupId AND user_id = $userId";
        $result = $this->db->select($query);
        
        if (!empty($result)) {
            return [
                'success' => false,
                'message' => 'أنت عضو بالفعل في هذه المجموعة'
            ];
        }
        
        // Add user to group
        $query = "INSERT INTO group_members (group_id, user_id, joined_at) 
                  VALUES ($groupId, $userId, NOW())";
        $result = $this->db->insert($query);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'تم الانضمام إلى المجموعة بنجاح'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'حدث خطأ أثناء الانضمام إلى المجموعة'
            ];
        }
    }
    
    public function leaveGroup($groupId, $userId) {
        $query = "DELETE FROM group_members WHERE group_id = $groupId AND user_id = $userId";
        $result = $this->db->delete($query);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'تم مغادرة المجموعة بنجاح'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'حدث خطأ أثناء مغادرة المجموعة'
            ];
        }
    }
    
    public function getGroupMembers($groupId) {
        $query = "SELECT gm.*, u.username, u.email, u.reputation, u.profile_picture
                  FROM group_members gm
                  JOIN users u ON gm.user_id = u.id
                  WHERE gm.group_id = $groupId
                  ORDER BY gm.is_admin DESC, u.username ASC";
        $result = $this->db->select($query);
        return $result;
    }
    
    public function getUserGroups($userId) {
        $query = "SELECT g.*, gm.joined_at, gm.is_admin
                  FROM groups g
                  JOIN group_members gm ON g.id = gm.group_id
                  WHERE gm.user_id = $userId
                  ORDER BY g.name ASC";
        $result = $this->db->select($query);
        return $result;
    }
    
    public function isGroupMember($groupId, $userId) {
        $query = "SELECT * FROM group_members WHERE group_id = $groupId AND user_id = $userId";
        $result = $this->db->select($query);
        return !empty($result);
    }
    
    public function isGroupAdmin($groupId, $userId) {
        $query = "SELECT * FROM group_members WHERE group_id = $groupId AND user_id = $userId AND is_admin = 1";
        $result = $this->db->select($query);
        return !empty($result);
    }
}
?>
