<?php
// controllers/authc.php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/rh.php';

class AuthController
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    // ========================
    // REGISTER USER
    // ========================
    public function register()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            empty($data['name']) ||
            empty($data['email']) ||
            empty($data['password'])
        ) {
            ResponseHelper::error("All fields are required", 400);
        }

        $name = htmlspecialchars(strip_tags($data['name']));
        $email = htmlspecialchars(strip_tags($data['email']));
        $password = password_hash($data['password'], PASSWORD_BCRYPT);

        // Check if email exists
        $checkQuery = "SELECT id FROM users WHERE email = :email";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(":email", $email);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            ResponseHelper::error("Email already registered", 409);
        }

        $query = "INSERT INTO users (name, email, password)
                  VALUES (:name, :email, :password)";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $password);

        if ($stmt->execute()) {
            ResponseHelper::success("User registered successfully");
        } else {
            ResponseHelper::error("Registration failed", 500);
        }
    }

    // ========================
    // LOGIN USER
    // ========================
    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['email']) || empty($data['password'])) {
            ResponseHelper::error("Email and password required", 400);
        }

        $email = htmlspecialchars(strip_tags($data['email']));
        $password = $data['password'];

        $query = "SELECT id, name, email, password FROM users WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            ResponseHelper::error("Invalid credentials", 401);
        }

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($password, $user['password'])) {
            ResponseHelper::error("Invalid credentials", 401);
        }

        // Simple token (can replace with JWT later)
        $token = bin2hex(random_bytes(32));

        ResponseHelper::success("Login successful", [
            "token" => $token,
            "user" => [
                "id" => $user['id'],
                "name" => $user['name'],
                "email" => $user['email']
            ]
        ]);
    }

    // ========================
    // LOGOUT (OPTIONAL)
    // ========================
    public function logout()
    {
        ResponseHelper::success("Logout successful");
    }
}
