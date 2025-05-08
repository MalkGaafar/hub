<?php

class Badge {
    private $id;
    private $name;
    private $description;
    private $imagePath;
    private $requiredReputation;
    private $createdAt;
    
    public function __construct($data = []) {
        $this->id = isset($data['id']) ? $data['id'] : null;
        $this->name = isset($data['name']) ? $data['name'] : '';
        $this->description = isset($data['description']) ? $data['description'] : '';
        $this->imagePath = isset($data['image_path']) ? $data['image_path'] : '';
        $this->requiredReputation = isset($data['required_reputation']) ? $data['required_reputation'] : 0;
        $this->createdAt = isset($data['created_at']) ? $data['created_at'] : null;
    }
    
    // Getters and setters
    public function getId() {
        return $this->id;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function setDescription($description) {
        $this->description = $description;
    }
    
    public function getImagePath() {
        return $this->imagePath;
    }
    
    public function setImagePath($imagePath) {
        $this->imagePath = $imagePath;
    }
    
    public function getRequiredReputation() {
        return $this->requiredReputation;
    }
    
    public function setRequiredReputation($requiredReputation) {
        $this->requiredReputation = $requiredReputation;
    }
    
    public function getCreatedAt() {
        return $this->createdAt;
    }
}
?>
