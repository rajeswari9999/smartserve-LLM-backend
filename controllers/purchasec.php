<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/rh.php';

class PurchaseController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    public function addPurchase()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['user_id']) || empty($data['items'])) {
            ResponseHelper::error("Required fields missing", 400);
        }

        $query = "INSERT INTO purchases (user_id, items)
                  VALUES (:user_id, :items)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":user_id", $data['user_id']);
        $stmt->bindParam(":items", $data['items']); // JSON/Text

        $stmt->execute()
            ? ResponseHelper::success("Purchase list saved successfully")
            : ResponseHelper::error("Failed to save purchase list", 500);
    }

    public function getPurchases($user_id)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM purchases WHERE user_id = :uid ORDER BY created_at DESC"
        );
        $stmt->bindParam(":uid", $user_id);
        $stmt->execute();

        ResponseHelper::success("Purchase lists fetched", $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}
