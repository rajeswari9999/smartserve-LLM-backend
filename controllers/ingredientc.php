<?php
// controllers/ingredientc.php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/rh.php';

class IngredientController
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    // =========================
    // ADD INGREDIENT
    // =========================
    public function addIngredient()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            empty($data['user_id']) ||
            empty($data['name']) ||
            empty($data['quantity']) ||
            empty($data['unit'])
        ) {
            ResponseHelper::error("Required fields missing", 400);
        }

        $query = "INSERT INTO ingredients 
                  (user_id, name, quantity, unit)
                  VALUES (:user_id, :name, :quantity, :unit)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":user_id", $data['user_id']);
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":quantity", $data['quantity']);
        $stmt->bindParam(":unit", $data['unit']);

        if ($stmt->execute()) {
            ResponseHelper::success("Ingredient added successfully");
        } else {
            ResponseHelper::error("Failed to add ingredient", 500);
        }
    }

    // =========================
    // GET USER INGREDIENTS
    // =========================
    public function getIngredientsByUser($user_id)
    {
        if (empty($user_id)) {
            ResponseHelper::error("User ID required", 400);
        }

        $query = "SELECT id, name, quantity, unit, created_at
                  FROM ingredients
                  WHERE user_id = :user_id
                  ORDER BY created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($ingredients) {
            ResponseHelper::success("Ingredients fetched successfully", $ingredients);
        } else {
            ResponseHelper::error("No ingredients found", 404);
        }
    }

    // =========================
    // UPDATE INGREDIENT
    // =========================
    public function updateIngredient($ingredient_id)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            empty($ingredient_id) ||
            empty($data['name']) ||
            empty($data['quantity']) ||
            empty($data['unit'])
        ) {
            ResponseHelper::error("Required fields missing", 400);
        }

        $query = "UPDATE ingredients
                  SET name = :name,
                      quantity = :quantity,
                      unit = :unit
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":quantity", $data['quantity']);
        $stmt->bindParam(":unit", $data['unit']);
        $stmt->bindParam(":id", $ingredient_id);

        if ($stmt->execute()) {
            ResponseHelper::success("Ingredient updated successfully");
        } else {
            ResponseHelper::error("Failed to update ingredient", 500);
        }
    }

    // =========================
    // DELETE INGREDIENT
    // =========================
    public function deleteIngredient($ingredient_id)
    {
        if (empty($ingredient_id)) {
            ResponseHelper::error("Ingredient ID required", 400);
        }

        $query = "DELETE FROM ingredients WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $ingredient_id);

        if ($stmt->execute()) {
            ResponseHelper::success("Ingredient deleted successfully");
        } else {
            ResponseHelper::error("Failed to delete ingredient", 500);
        }
    }
}
