<?php
// controllers/favoritec.php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/rh.php';

class FavoriteController
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    // =========================
    // ADD TO FAVORITES
    // =========================
    public function addFavorite()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            empty($data['user_id']) ||
            empty($data['item_type']) ||
            empty($data['item_id']) ||
            empty($data['content'])
        ) {
            ResponseHelper::error("All fields are required", 400);
        }

        $query = "INSERT INTO favorites 
                  (user_id, item_type, item_id, content)
                  VALUES (:user_id, :item_type, :item_id, :content)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":user_id", $data['user_id']);
        $stmt->bindParam(":item_type", $data['item_type']); // recipe, menu, marketing, etc.
        $stmt->bindParam(":item_id", $data['item_id']);
        $stmt->bindParam(":content", $data['content']);

        if ($stmt->execute()) {
            ResponseHelper::success("Added to favorites successfully");
        } else {
            ResponseHelper::error("Failed to add favorite", 500);
        }
    }

    // =========================
    // GET USER FAVORITES
    // =========================
    public function getFavoritesByUser($user_id)
    {
        if (empty($user_id)) {
            ResponseHelper::error("User ID required", 400);
        }

        $query = "SELECT * FROM favorites 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ResponseHelper::success("Favorites fetched successfully", $favorites);
    }

    // =========================
    // REMOVE FROM FAVORITES
    // =========================
    public function removeFavorite($favorite_id)
    {
        if (empty($favorite_id)) {
            ResponseHelper::error("Favorite ID required", 400);
        }

        $query = "DELETE FROM favorites WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $favorite_id);

        if ($stmt->execute()) {
            ResponseHelper::success("Favorite removed successfully");
        } else {
            ResponseHelper::error("Failed to remove favorite", 500);
        }
    }
}
