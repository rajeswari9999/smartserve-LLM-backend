<?php
class AuthHelper
{
    // Hash password
    public static function hashPassword($password)
    {
        if (empty($password)) {
            return [
                "status" => "failed",
                "message" => "Password is empty"
            ];
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);

        if ($hash) {
            return [
                "status" => "success",
                "message" => "AuthHelper ran successfully",
                "hashed_password" => $hash
            ];
        }

        return [
            "status" => "failed",
            "message" => "Hashing failed"
        ];
    }

    // Verify password
    public static function verifyPassword($password, $hash)
    {
        if (!$password || !$hash) {
            return [
                "status" => "failed",
                "message" => "Missing password or hash"
            ];
        }

        if (password_verify($password, $hash)) {
            return [
                "status" => "success",
                "message" => "Password verified successfully"
            ];
        }

        return [
            "status" => "failed",
            "message" => "Password verification failed"
        ];
    }

    // Authorization check
    public static function checkAuth()
    {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            return [
                "status" => "failed",
                "message" => "Authorization header missing"
            ];
        }

        return [
            "status" => "success",
            "message" => "AuthHelper authorization check successful"
        ];
    }
}

/* ---------- DIRECT TEST OUTPUT (REMOVE IN PRODUCTION) ---------- */
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    header("Content-Type: application/json");
    echo json_encode(AuthHelper::hashPassword("SmartServe123"));
}
