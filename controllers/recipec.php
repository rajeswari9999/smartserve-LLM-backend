<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/rh.php';

class RecipeController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    public function saveRecipe()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['user_id']) || empty($data['recipe_name']) || empty($data['details'])) {
            ResponseHelper::error("Required fields missing", 400);
        }

        $stmt = $this->db->prepare(
            "INSERT INTO recipes (user_id, recipe_name, details)
             VALUES (:uid, :name, :details)"
        );

        $stmt->bindParam(":uid", $data['user_id']);
        $stmt->bindParam(":name", $data['recipe_name']);
        $stmt->bindParam(":details", $data['details']);

        $stmt->execute()
            ? ResponseHelper::success("Recipe saved successfully")
            : ResponseHelper::error("Failed to save recipe", 500);
    }

    public function getRecipes($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM recipes WHERE user_id = :uid");
        $stmt->bindParam(":uid", $user_id);
        $stmt->execute();

        ResponseHelper::success("Recipes fetched", $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}
