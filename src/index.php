<?php
use \core\Util;
use \core\APIResponse;

spl_autoload_register(function ($class) {
    $nameparts = explode("\\", $class);
    $filename = array_pop($nameparts) . '.php';
    $path = 'classes/' . strtolower(implode('/', $nameparts));

    require_once "{$path}/{$filename}";
});

//Parse API path into a namespace and class name
$parsed = Util::parse_api_path($_REQUEST['api']);
$classname = "\\api\\{$parsed['component']}\\{$parsed['action']}";
$request = new $classname();
$function = strtoupper($_SERVER['REQUEST_METHOD']);

//Retrieve request body, and, if it exists put it to the front of the arguments list
$request_body = file_get_contents('php://input');
$args = empty($request_body) ? $parsed["args"] : [$request_body, ...$parsed["args"]];

//Call the API method
try {
    if (!in_array($function, ['GET', 'POST'])) {
        throw new \core\MethodNotSupportedException();
    }

    $result = $request->$function(...$args);
}
catch (\core\MethodNotSupportedException $e) {
    $result = new APIResponse(json_encode(["message" => "Method not allowed at this point"]), 405);
}
catch (Exception $e) {
    $result = new APIResponse(json_encode(["message" => "Unexpected Error"]), 500);
}

//Output response
http_response_code($result->response());

foreach ($result->headers() as $header) {
    header($header);
}

echo $result->body();

exit();
