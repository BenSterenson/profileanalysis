<?php
require_once 'abstract_api.php';
class API extends abstract_api
{
    public function __construct($request, $origin) {
        parent::__construct($request);

    }

    /**
     * Get Eye Color
     */
     protected function getEyeColors() {
        if ($this->method == 'GET') {
            return "Success";
        } else {
            return "Only accepts GET requests";
        }
     }
 }
 
 // Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
    $API = new API($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
    echo $API->processAPI();
} catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}
 ?>