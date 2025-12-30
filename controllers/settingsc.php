<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/rh.php';

class SettingsController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    public function updateSettings()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['user_id']) || empty($data['settings'])) {
            ResponseHelper::error("Required fields missing", 400);
        }

        $stmt = $this->db->prepare(
            "UPDATE users SET settings = :settings WHERE id = :uid"
        );

        $stmt->bindParam(":settings", $data['settings']); // JSON
        $stmt->bindParam(":uid", $data['user_id']);

        $stmt->execute()
            ? ResponseHelper::success("Settings updated successfully")
            : ResponseHelper::error("Failed to update settings", 500);
    }
}
