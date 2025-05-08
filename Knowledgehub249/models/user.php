<?php
class User {
    private $id;
    private $username;
    private $email;
    private $password;
    private $reputation;
    private $isAdmin;
    private $createdAt;
    
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->username = $data['username'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->password = $data['password'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->reputation = $data['reputation'] ?? null;
    }

    
    // Getters and setters
    public function getId() {
        return $this->id;
    }
    
    public function getUsername() {
        return $this->username;
    }
    
    public function setUsername($username) {
        $this->username = $username;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function setEmail($email) {
        $this->email = $email;
    }
    
    public function getPassword() {
        return $this->password;
    }
    
    public function setPassword($password) {
        $this->password = $password;
    }
    
    public function getReputation() {
        return $this->reputation;
    }
    
    public function setReputation($reputation) {
        $this->reputation = $reputation;
    }
    
    public function isAdmin() {
        return $this->isAdmin == 1;
    }
    
    public function getCreatedAt() {
        return $this->createdAt;
    }
}
?>

