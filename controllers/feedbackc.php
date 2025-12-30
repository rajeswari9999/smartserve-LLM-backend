<?php
// controllers/feedbackc.php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/rh.php';

class FeedbackController
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    // =========================
    // SUBMIT FEEDBACK
    // =========================
    public function submitFeedback()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            empty($data['user_id']) ||
            empty($data['rating']) ||
            empty($data['message'])
        ) {
            ResponseHelper::error("User ID, rating and message are required", 400);
        }

        $rating = (int) $data['rating'];

        if ($rating < 1 || $rating > 5) {
            ResponseHelper::error("Rating must be between 1 and 5", 400);
        }

        $query = "INSERT INTO feedback 
                  (user_id, rating, message)
                  VALUES (:user_id, :rating, :message)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":user_id", $data['user_id']);
        $stmt->bindParam(":rating", $rating);
        $stmt->bindParam(":message", $data['message']);

        if ($stmt->execute()) {
            ResponseHelper::success("Feedback submitted successfully");
        } else {
            ResponseHelper::error("Failed to submit feedback", 500);
        }
    }

    // =========================
    // GET ALL FEEDBACK (ADMIN)
    // =========================
    public function getAllFeedback()
    {
        $query = "SELECT f.id, f.rating, f.message, f.created_at, 
                         u.name AS user_name, u.email
                  FROM feedback f
                  JOIN users u ON f.user_id = u.id
                  ORDER BY f.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ResponseHelper::success("Feedback retrieved successfully", $feedback);
    }

    // =========================
    // GET FEEDBACK BY USER
    // =========================
    public function getFeedbackByUser($user_id)
    {
        if (empty($user_id)) {
            ResponseHelper::error("User ID required", 400);
        }

        $query = "SELECT id, rating, message, created_at
                  FROM feedback
                  WHERE user_id = :user_id
                  ORDER BY created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        $feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ResponseHelper::success("User feedback retrieved successfully", $feedback);
    }
}
