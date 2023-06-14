<?php
namespace api\receipts;

use core\APIResponse;
use \core\Util;

class Points extends \Core\APIRequest {
    /**
     * @param ...$args mixed Only expects a receipt ID
     * @return \Core\APIResponse
     */
    public function GET(...$args) : \Core\APIResponse {
        //Validate request
        if (!isset($args[0])) {
            return new APIResponse('', 400);
        }

        //Retrieve receipt and calculate points
        $mem = Util::get_cache();
        $receipt = $mem->get($args[0]);

        if ($receipt === false) {
            if ($mem->getResultCode() === \Memcached::RES_NOTFOUND) {
                return new APIResponse(json_encode(["message" => "No receipt with that ID"]), 404, ["Content-Type: application/json"]);
            }
            else {
                //Some other error
                return new APIResponse(json_encode(["message" => "Error retrieving receipt.", "response" => $mem->getResultMessage()]), 500, ["Content-Type: application/json"]);
            }
        }

        //Return response
        $body = [
            "points" => $this->calculatePoints(json_decode($receipt, false))
        ];

        $headers = [
            "Content-Type: application/json"
        ];

        return new APIResponse(json_encode($body), 200, $headers);
    }

    /**
     * Calculates the points of a receipt object
     *
     * @param object $receipt An object conforming to the API's receipt schema
     * @return int The number of points the receipt is worth
     */
    private function calculatePoints(object $receipt) : int {
        $points = 0;

        //One point for every alphanumeric character in the retailer name.
        $points += strlen(preg_replace('/[^0-9a-zA-Z]/', '', $receipt->retailer));

        //50 points if the total is a round dollar amount with no cents.
        if (preg_match('/\.00$/', $receipt->total) === 1) {
            $points += 50;
        }

        //25 points if the total is a multiple of 0.25.
        if (preg_match('/\.(00|25|50|75)$/', $receipt->total) === 1) {
            $points += 25;
        }

        //5 points for every two items on the receipt.
        $points += 5 * intdiv(count($receipt->items), 2);

        //If the trimmed length of the item description is a multiple of 3, multiply the price by 0.2 and round up to the nearest integer. The result is the number of points earned.
        foreach ($receipt->items as $item) {
            $length = mb_strlen(trim($item->shortDescription));

            if ($length % 3 == 0) {
                $points += ceil(((float)$item->price) * 0.2);
            }
        }

        //6 points if the day in the purchase date is odd.
        $day = (int)(substr($receipt->purchaseDate, -2)); //Day is the last two digits

        if ($day % 2 == 1) {
            $points += 6;
        }

        //10 points if the time of purchase is after 2:00pm and before 4:00pm.
        if (preg_match('/^(14|15)/', $receipt->purchaseTime)) {
            $points += 10;
        }

        return $points;
    }
}