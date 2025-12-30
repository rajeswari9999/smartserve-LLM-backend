<?php
// controllers/marketingc.php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/rh.php';

class MarketingController
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    // =========================
    // GENERATE & SAVE MARKETING CONTENT
    // =========================
    public function saveMarketingContent()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            empty($data['user_id']) ||
            empty($data['title']) ||
            empty($data['content_type']) ||
            empty($data['content'])
        ) {
            ResponseHelper::error("Required fields missing", 400);
        }

        $query = "INSERT INTO marketing_content
                  (user_id, title, content_type, content)
                  VALUES (:user_id, :title, :content_type, :content)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":user_id", $data['user_id']);
        $stmt->bindParam(":title", $data['title']);          // Campaign name
        $stmt->bindParam(":content_type", $data['content_type']); // Caption, Email, Ad text
        $stmt->bindParam(":content", $data['content']);      // AI generated text

        if ($stmt->execute()) {
            ResponseHelper::success("Marketing content saved successfully");
        } else {
            ResponseHelper::error("Failed to save marketing content", 500);
        }
    }

    // =========================
    // GET MARKETING CONTENT BY USER
    // =========================
    public function getMarketingByUser($user_id)
    {
        if (empty($user_id)) {
            ResponseHelper::error("User ID required", 400);
        }

        $query = "SELECT id, title, content_type, content, created_at
                  FROM marketing_content
                  WHERE user_id = :user_id
                  ORDER BY created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        $marketing = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($marketing) {
            ResponseHelper::success("Marketing content fetched successfully", $marketing);
        } else {
            ResponseHelper::error("No marketing content found", 404);
        }
    }

    // =========================
    // DELETE MARKETING CONTENT
    // =========================
    public function deleteMarketingContent($content_id)
    {
        if (empty($content_id)) {
            ResponseHelper::error("Content ID required", 400);
        }

        $query = "DELETE FROM marketing_content WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $content_id);

        if ($stmt->execute()) {
            ResponseHelper::success("Marketing content deleted successfully");
        } else {
            ResponseHelper::error("Failed to delete marketing content", 500);
        }
    }
}
