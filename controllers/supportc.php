<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/rh.php';

class SupportController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    public function sendMessage()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['user_id']) || empty($data['message'])) {
            ResponseHelper::error("Required fields missing", 400);
        }

        $stmt = $this->db->prepare(
            "INSERT INTO support_messages (user_id, message)
             VALUES (:uid, :message)"
        );

        $stmt->bindParam(":uid", $data['user_id']);
        $stmt->bindParam(":message", $data['message']);

        $stmt->execute()
            ? ResponseHelper::success("Support message sent successfully")
            : ResponseHelper::error("Failed to send support message", 500);
    }
}
