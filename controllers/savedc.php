<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/rh.php';

class SavedController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    public function saveItem()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['user_id']) || empty($data['content'])) {
            ResponseHelper::error("Required fields missing", 400);
        }

        $stmt = $this->db->prepare(
            "INSERT INTO saved_items (user_id, content)
             VALUES (:uid, :content)"
        );

        $stmt->bindParam(":uid", $data['user_id']);
        $stmt->bindParam(":content", $data['content']);

        $stmt->execute()
            ? ResponseHelper::success("Item saved successfully")
            : ResponseHelper::error("Failed to save item", 500);
    }

    public function getSavedItems($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM saved_items WHERE user_id = :uid");
        $stmt->bindParam(":uid", $user_id);
        $stmt->execute();

        ResponseHelper::success("Saved items fetched", $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}
