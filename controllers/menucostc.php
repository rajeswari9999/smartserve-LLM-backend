<?php
// controllers/menucostc.php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/rh.php';

class MenuCostController
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    // =========================
    // ADD MENU COST
    // =========================
    public function addMenuCost()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            empty($data['user_id']) ||
            empty($data['menu_name']) ||
            empty($data['ingredients']) ||
            empty($data['total_cost'])
        ) {
            ResponseHelper::error("Required fields missing", 400);
        }

        $query = "INSERT INTO menu_costs
                  (user_id, menu_name, ingredients, total_cost)
                  VALUES (:user_id, :menu_name, :ingredients, :total_cost)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":user_id", $data['user_id']);
        $stmt->bindParam(":menu_name", $data['menu_name']);
        $stmt->bindParam(":ingredients", $data['ingredients']); // JSON or text
        $stmt->bindParam(":total_cost", $data['total_cost']);

        if ($stmt->execute()) {
            ResponseHelper::success("Menu cost added successfully");
        } else {
            ResponseHelper::error("Failed to add menu cost", 500);
        }
    }

    // =========================
    // GET MENU COST BY USER
    // =========================
    public function getMenuCostByUser($user_id)
    {
        if (empty($user_id)) {
            ResponseHelper::error("User ID required", 400);
        }

        $query = "SELECT id, menu_name, ingredients, total_cost, created_at
                  FROM menu_costs
                  WHERE user_id = :user_id
                  ORDER BY created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        $menuCosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($menuCosts) {
            ResponseHelper::success("Menu costs fetched successfully", $menuCosts);
        } else {
            ResponseHelper::error("No menu cost records found", 404);
        }
    }

    // =========================
    // UPDATE MENU COST
    // =========================
    public function updateMenuCost($menu_id)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            empty($menu_id) ||
            empty($data['menu_name']) ||
            empty($data['ingredients']) ||
            empty($data['total_cost'])
        ) {
            ResponseHelper::error("Required fields missing", 400);
        }

        $query = "UPDATE menu_costs
                  SET menu_name = :menu_name,
                      ingredients = :ingredients,
                      total_cost = :total_cost
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":menu_name", $data['menu_name']);
        $stmt->bindParam(":ingredients", $data['ingredients']);
        $stmt->bindParam(":total_cost", $data['total_cost']);
        $stmt->bindParam(":id", $menu_id);

        if ($stmt->execute()) {
            ResponseHelper::success("Menu cost updated successfully");
        } else {
            ResponseHelper::error("Failed to update menu cost", 500);
        }
    }

    // =========================
    // DELETE MENU COST
    // =========================
    public function deleteMenuCost($menu_id)
    {
        if (empty($menu_id)) {
            ResponseHelper::error("Menu ID required", 400);
        }

        $query = "DELETE FROM menu_costs WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $menu_id);

        if ($stmt->execute()) {
            ResponseHelper::success("Menu cost deleted successfully");
        } else {
            ResponseHelper::error("Failed to delete menu cost", 500);
        }
    }
}
