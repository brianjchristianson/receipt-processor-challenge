<?php

namespace Core;

/**
 * Simplified API request. Only guaranteed to recognize GET or Support methods. All methods throw a MethodNotSupportedException if not overridden
 */
abstract class APIRequest
{

    /**
     * Request handling for an HTTP GET.
     *
     * @param ...$args mixed Any arguments passed to the request
     * @return APIResponse The result of the request
     * @throws MethodNotSupportedException
     */
    public function GET(...$args) : APIResponse {
        throw new MethodNotSupportedException("HTTP GET not supported at this endpoint");
    }

    /**
     * Request handling for an HTTP POST.
     *
     * @param ...$args mixed Any arguments passed to the request. The first argument is expected to be the POST body.
     * @return APIResponse The result of the request
     * @throws MethodNotSupportedException
     */
    public function POST(...$args) : APIResponse {
        throw new MethodNotSupportedException("HTTP POST not supported at this endpoint");
    }
}