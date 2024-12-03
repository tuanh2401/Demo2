<?php
class Category {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function getAllCategories() {
        $sql = "SELECT * FROM categories";
        return $this->db->query($sql)->fetchAll();
    }
}
