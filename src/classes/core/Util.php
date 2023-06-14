<?php

namespace Core;

/**
 * A collection of utility functions
 */
class Util
{
    static $RECEIPT_CACHE = "Receipts";

    /**
     * Checks if the receipt matches the API definition
     *
     * @param string $json JSON string representation of the receipt
     * @return bool True if valid, false otherwise
     */
    static public function is_receipt_valid(string $json) : bool {
        $receipt = json_decode($json, false);

        if (!is_object($receipt)) {
            return false;
        }

        if (!property_exists($receipt, 'retailer') || !is_string($receipt->retailer)) {
            return false;
        }

        if (!property_exists($receipt, 'purchaseDate') || !is_string($receipt->purchaseDate)) {
            return false;
        }
        elseif (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $receipt->purchaseDate)) {
            return false;
        }

        if (!property_exists($receipt, 'purchaseTime') || !is_string($receipt->purchaseTime)) {
            return false;
        } elseif (!preg_match('/^(([01][0-9]|[2][0-3]):[0-5][0-9])|24:00$/', $receipt->purchaseTime)) {
            return false;
        }

        if (!property_exists($receipt, 'total') || !is_string($receipt->total)) {
            return false;
        }  elseif (!preg_match('/^\d+\.\d{2}$/', $receipt->total)) {
            return false;
        }

        if (!property_exists($receipt, 'items') || !is_array($receipt->items) || count($receipt->items) < 1) {
            return false;
        }

        foreach ($receipt->items as $item) {
            if (!static::is_item_valid($item)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if a receipt item matches the API definition
     *
     * @param object $item An object representation of a receipt item
     * @return bool True if valid, False otherwise
     */
    public static function is_item_valid(object $item) : bool {
        if (!property_exists($item, 'shortDescription') || !is_string($item->shortDescription)) {
            return false;
        }
        elseif (!preg_match('/^[\w\s\-]+$/', $item->shortDescription)) {
            return false;
        }

        if (!property_exists($item, 'price') || !is_string($item->price)) {
            return false;
        }
        elseif (!preg_match('/^\d+\.\d{2}$/', $item->price)) {
            return false;
        }

        return true;
    }

    /**
     * Translates a string API path into an array
     *
     * @param string $path
     * @return array Includes "component", "action", and "args"
     */
    public static function parse_api_path(string $path) : array {
        $parts = explode("/", $path);
        return [
            "component" => array_shift($parts),
            "action" => array_pop($parts),
            "args" => $parts
        ];
    }

    public static function get_cache() : \Memcached {
        $mem = new \Memcached(Util::$RECEIPT_CACHE);
        $server = 'receipt-cache';
        $port = 11211;
        $mem->addServer($server, $port);

        return $mem;
    }
}