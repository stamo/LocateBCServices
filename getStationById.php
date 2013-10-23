<?php   
    require_once 'Includes/bcHireAvailability.php';
    require_once 'Includes/mongoDB.php';
    require_once 'Includes/httpResponceHelper.php';
    require_once 'Includes/returnModels.php';
    require_once 'Includes/googleRequests.php';

    $http_code = 200;
    $error_message = "";
    $isDataUpToDate = true;
    
    if (array_key_exists("id", $_GET) && 
            array_key_exists("lat", $_GET) && 
            array_key_exists("long", $_GET)) {
        $id = (int)$_GET["id"];
        $user_lat =  $_GET["lat"];
        $user_long = $_GET["long"];
    } else {
        $http_code = 400;
        http_response_code($http_code);
        die("Id, Longitude and Latitude are required!");
    }
    
    if (array_key_exists("units", $_GET)) {
        $units = $_GET["units"];
    } else {
        $units = "imperial";
    }
    
    try {
        $db_src = new MyMONGODB();
    } catch (Exception $e) {
        $http_code = 500;
        $error_message = $e->getMessage();
        http_response_code($http_code);
        die($error_message);
    }
    
    try {
        $web_src_bc = new BCHIRE();
    } catch (Exception $e) {
        $isDataUpToDate = false;
    }
    
    if ($isDataUpToDate) {
        $db_src->saveStations($web_src_bc->getData());
    }
    
    $stations_from_db = $db_src->getStationById($id);
    
    $return_model = RETURNMODELS::getSingleStationsModel($stations_from_db, $web_src_bc->time, $isDataUpToDate);
    $distance_calculator = new GOOGLEREQUESTS($units);
    $return_model["stations"] = $distance_calculator->calculateDistances($user_lat, $user_long, $return_model["stations"]);
    
    http_response_code($http_code);
    if ($_SERVER["HTTP_ACCEPT"] == "application/xml") {
        require_once 'Includes/XMLSerializer.php';
        $xml = new XMLSerializer();
        echo $xml->generateValidXmlFromArray($return_model);
    } else {
        echo json_encode($return_model);
    }
?>
