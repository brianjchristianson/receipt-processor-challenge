<?php
namespace api\receipts;

use \core\Util;
use \core\APIResponse;

/**
 * The Receipt Process endpoint. Recognizes POST requests, and stores the receipt. Returns a JSON object with the assigned receipt ID
 */
class Process extends \Core\APIRequest {
    /**
     * @param ...$args mixed Only expected argument is a JSON string representation of a receipt
     * @return APIResponse
     * @throws \Exception If unable to store the receipt
     */
    public function POST(...$args) : \Core\APIResponse {
        //Validate request
        if (!isset($args[0])) {
            return new APIResponse(json_encode(["message" => "No receipt provided"]), 400, ["Content-Type: application/json"]);
        }

        if (!Util::is_receipt_valid($args[0])) {
            return new APIResponse(json_encode(["message" => "Invalid receipt format"]), 400, ["Content-Type: application/json"]);
        }

        //Store receipt
        $mem = Util::get_cache();

        $addResult = false;
        $id = bin2hex(random_bytes(32));

        //Loop storage attempts for the unlikely case of an id collision
        while ($addResult === false) {
            $addResult = $mem->add($id, $args[0]);

            if ($addResult === false) {
                if ($mem->getResultCode() == \Memcached::RES_NOTSTORED) {
                    //Try a different ID
                    $id = bin2hex(random_bytes(32));
                }
                else {
                    //Some other error that can't be solved by new attempts to store
                    return new APIResponse(json_encode(["message" => "Error storing receipt."]), 500, ["Content-Type: application/json"]);
                }

            }
        }

        //Build and return API response
        $body = [
            "id" => $id
        ];

        $headers = [
            "Content-Type: application/json"
        ];

        return new APIResponse(json_encode($body), 200, $headers);
    }
}