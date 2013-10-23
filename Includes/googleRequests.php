<?php
    class GOOGLEREQUESTS {
        
        private $distance_url;

        public function __construct($units) {
            $this->distance_url = "http://maps.googleapis.com/maps/api/distancematrix/json?sensor=true&units={$units}&";
        }
        
        private function getDistanceMatrix($origin, $destinations, $mode) {
            $ch = curl_init("{$this->distance_url}origins={$origin}&destinations={$destinations}&mode={$mode}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $json = curl_exec($ch);
            curl_close($ch);
            
            if ($json) {
                $data = json_decode($json, true);
                return $data;
            } else {
                throw new Exception("No connection to service");
            }
        }
        
        private function populateDistancesInModels($distance_matrix_walking, $distance_matrix_bicycling, $stations) {
            if ($distance_matrix_walking["status"] == "OK") {
                $matrix_walking = $distance_matrix_walking["rows"][0]["elements"];
            } else {
                $matrix_walking = false;
            }

            if ($distance_matrix_bicycling["status"] == "OK") {
                $matrix_bicyling = $distance_matrix_bicycling["rows"][0]["elements"];
            } else {
                $matrix_bicyling = false;
            }
            for ($index = 0; $index < count($stations); $index++) {
                if ($matrix_bicyling && $matrix_bicyling[$index]["status"] == "OK") {
                    $stations[$index]["adress"] = $distance_matrix_bicycling["destination_addresses"][$index];
                    $stations[$index]["distanceCycle"] = $matrix_bicyling[$index]["distance"]["text"];
                    $stations[$index]["timeCycle"] = $matrix_bicyling[$index]["duration"]["text"];
                }
                
                if ($matrix_walking && $matrix_walking[$index]["status"] == "OK") {
                    $stations[$index]["adress"] = $distance_matrix_walking["destination_addresses"][$index];
                    $stations[$index]["distanceWalk"] = $matrix_walking[$index]["distance"]["text"];
                    $stations[$index]["timeWalk"] = $matrix_walking[$index]["duration"]["text"];
                }
            }
            
            return $stations;
        }
        
        public function calculateDistances($lat, $long, $stations) {
            $origin = "{$lat},{$long}";
            $destinations_array = array();
            foreach ($stations as $station) {
                array_push($destinations_array, "{$station["lat"]},{$station["long"]}");
            }
            
            $destinations = implode("|", $destinations_array);
            $distance_matrix_walking = $this->getDistanceMatrix($origin, $destinations, "walking");
            $distance_matrix_bicycling = $this->getDistanceMatrix($origin, $destinations, "bicycling");
            return $this->populateDistancesInModels($distance_matrix_walking, $distance_matrix_bicycling, $stations);
        }
    }
?>