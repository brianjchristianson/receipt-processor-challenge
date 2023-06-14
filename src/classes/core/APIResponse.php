<?php

namespace Core;

/**
 * A basic representation of an API response. Immutable.
 */
class APIResponse
{
    private $body;
    private $response;
    private $headers;

    /**
     * @param string $body The response body
     * @param int $response HTTP response code
     * @param array $headers Array of headers, formatted as strings
     */
    public function __construct(string $body, int $response = 200, array $headers = []) {
        $this->body = $body;
        $this->response = $response;
        $this->headers = $headers;
    }

    /**
     * @return string The response body
     */
    public function body() : string {
        return $this->body;
    }

    /**
     * @return int The response's HTTP code number
     */
    public function response() : int {
        return $this->response;
    }

    /**
     * @return array An array of HTTP headers in string format
     */
    public function headers() : array {
        return $this->headers;
    }
}