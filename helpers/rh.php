<?php
class ResponseHelper {

    // Sends JSON response and stops execution
    public static function send($data, $status = 200) {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit; // stop further script execution
    }

}
?>
