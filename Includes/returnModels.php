<?php
    class RETURNMODELS {
        public function getAllStationsModel($stations, $timestamp, $isDataUpToDate) {
            $model = array();
            $model["stations"] = array();
            if ($isDataUpToDate) {
                $model["timestamp"] = $timestamp;
            } else {
                $model["timestamp"] = 0;
            }
            
            foreach ($stations as $station) {
                $newItem = array(
                    "stationId" => $station["stationId"],
                    "name" => $station["name"],
                    "long" => $station["location"]["coordinates"][0],
                    "lat" => $station["location"]["coordinates"][1]
                );
                
                if ($isDataUpToDate) {
                    $newItem["freeBikes"] = $station["freeBikes"];
                    $newItem["freeDocks"] = $station["freeDocks"];
                } else {
                    $newItem["freeBikes"] = "N/A";
                    $newItem["freeDocks"] = "N/A";
                }
                
                array_push($model["stations"], $newItem);
            }
            
            return $model;
        }
        
        public function getClosestStationsModel($stations, $timestamp, $isDataUpToDate) {
            $model = array();
            $model["stations"] = array();
            if ($isDataUpToDate) {
                $model["timestamp"] = $timestamp;
            } else {
                $model["timestamp"] = 0;
            }
            
            foreach ($stations["results"] as $station) {
                $newItem = array(
                    "stationId" => $station["obj"]["stationId"],
                    "name" => $station["obj"]["name"],
                    "adress" => "N/A",
                    "long" => $station["obj"]["location"]["coordinates"][0],
                    "lat" => $station["obj"]["location"]["coordinates"][1],
                    "distanceWalk" => "N/A",
                    "timeWalk" => "N/A",
                    "distanceCycle" => "N/A",
                    "timeCycle" => "N/A"
                );
                
                if ($isDataUpToDate) {
                    $newItem["freeBikes"] = $station["obj"]["freeBikes"];
                    $newItem["freeDocks"] = $station["obj"]["freeDocks"];
                } else {
                    $newItem["freeBikes"] = "N/A";
                    $newItem["freeDocks"] = "N/A";
                }
                
                array_push($model["stations"], $newItem);
            }
            
            return $model;
        }
        
        public function getSingleStationsModel($stations, $timestamp, $isDataUpToDate) {
            $model = array();
            $model["stations"] = array();
            if ($isDataUpToDate) {
                $model["timestamp"] = $timestamp;
            } else {
                $model["timestamp"] = 0;
            }
            
            foreach ($stations as $station) {
                $newItem = array(
                    "stationId" => $station["stationId"],
                    "name" => $station["name"],
                    "adress" => "N/A",
                    "long" => $station["location"]["coordinates"][0],
                    "lat" => $station["location"]["coordinates"][1],
                    "distanceWalk" => "N/A",
                    "timeWalk" => "N/A",
                    "distanceCycle" => "N/A",
                    "timeCycle" => "N/A"
                );
                
                if ($isDataUpToDate) {
                    $newItem["freeBikes"] = $station["freeBikes"];
                    $newItem["freeDocks"] = $station["freeDocks"];
                } else {
                    $newItem["freeBikes"] = "N/A";
                    $newItem["freeDocks"] = "N/A";
                }
                
                array_push($model["stations"], $newItem);
            }
            
            return $model;
        }
    }
?>
