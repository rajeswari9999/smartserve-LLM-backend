<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/rh.php';

class NotificationController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    public function addNotification()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['user_id']) || empty($data['message'])) {
            ResponseHelper::error("Required fields missing", 400);
        }

        $query = "INSERT INTO notifications (user_id, message)
                  VALUES (:user_id, :message)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":user_id", $data['user_id']);
        $stmt->bindParam(":message", $data['message']);

        $stmt->execute()
            ? ResponseHelper::success("Notification added successfully")
            : ResponseHelper::error("Failed to add notification", 500);
    }

    public function getNotifications($user_id)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC"
        );
        $stmt->bindParam(":uid", $user_id);
        $stmt->execute();

        ResponseHelper::success("Notifications fetched", $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}
