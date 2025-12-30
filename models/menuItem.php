<?php
require_once __DIR__ . '/../config/db.php';

class MenuItem {

    private $conn;
    private $table = "menu_items";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Get all menu items
    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add new menu item
    public function create($name, $price) {
        $query = "INSERT INTO " . $this->table . " (name, price) VALUES (:name, :price)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);

        return $stmt->execute();
    }

    // Get menu item by ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
