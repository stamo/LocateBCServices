<?php
include_once 'Includes/journeyPlanner.php';
require_once 'Includes/httpResponceHelper.php';

$http_code = 200;

if (array_key_exists("origin_long", $_GET) && 
        array_key_exists("origin_lat", $_GET) && 
        array_key_exists("destination_long", $_GET) && 
        array_key_exists("destination_lat", $_GET)) {
    $origin_long = (float)$_GET["origin_long"];
    $origin_lat = (float)$_GET["origin_lat"];
    $destination_long = (float)$_GET["destination_long"];
    $destination_lat = (float)$_GET["destination_lat"];
} else {
    $http_code = 400;
    http_response_code($http_code);
    die("Origin and Destination coordinates are required!");
}

$journey = new JOURNEYPLANNER();
$origin = "{$origin_long}:{$origin_lat}";
$destination = "{$destination_long}:{$destination_lat}";

try {
    $route["routepoints"] = $journey->getCalculatedRoute($origin, $destination);
} catch (Exception $exc) {
    $http_code = 500;
    http_response_code($http_code);
    die($exc->getTraceAsString());
}

http_response_code($http_code);
echo json_encode($route);
?>
