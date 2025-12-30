<?php
class LLMHelper
{
    // Process prompt for SmartServe LLM
    public static function processPrompt($prompt, $type)
    {
        if (empty($prompt)) {
            return [
                "status" => "failed",
                "message" => "Prompt is empty"
            ];
        }

        $response = "";

        switch ($type) {
            case "recipe":
                $response = "Standardized recipe generated successfully.";
                break;

            case "menucost":
                $response = "Menu cost calculated successfully.";
                break;

            case "marketing":
                $response = "Marketing caption generated successfully.";
                break;

            case "purchase":
                $response = "Daily purchase list generated successfully.";
                break;

            default:
                return [
                    "status" => "failed",
                    "message" => "Invalid LLM request type"
                ];
        }

        return [
            "status" => "success",
            "message" => "LLMHelper ran successfully",
            "data" => $response
        ];
    }
}

/* ---------- DIRECT TEST OUTPUT (REMOVE IN PRODUCTION) ---------- */
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    header("Content-Type: application/json");
    echo json_encode(LLMHelper::processPrompt("Make a cake recipe", "recipe"));
}
