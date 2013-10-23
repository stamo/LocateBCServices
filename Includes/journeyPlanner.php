<?php
    class JOURNEYPLANNER {
        
        private $base_url;
        private $options;
        private $XML;

        public function __construct() {
            $this->base_url = "http://jpapi.tfl.gov.uk/api/XML_TRIP_REQUEST2";
            $this->options = array (
                "language" => "en",
                "sessionID" => 0,
                "place_origin" => "London",
                "type_origin" => "coord",
                "name_origin" => "",
                "place_destination" => "London",
                "type_destination" => "coord",
                "name_destination" => "",
                "advOptActive_2" => 1,
                "advOpt_2" => 1,
                "ptAdvancedOptions" => 1,
                "bikeProf" => "default",
                "bikeProfSpeed" => "default:12",
                "calcNumberOfTrips" => 1,
                "cycleSpeed" => 12,
                "cycleType" => 107,
                "cyclingActive" => 1,
                "itOptionsActive" => 1,
                "ptOptionsActive" => 1,
                "locationServerActive" => 1,
                "calcAltVarBicycle" => 1,
                "coordOutputFormat" => "WGS84[DD.DDDDD]|WGS84[DD,DDDDD]"
            );
            
        }
        
        public function getCalculatedRoute($origin, $destination) {
            $this->options["name_origin"] = $origin . ":WGS84[DD.DDDDD]"; // format: long:lat
            $this->options["name_destination"] = $destination . ":WGS84[DD.DDDDD]";
            $current_options = array();
            foreach ($this->options as $key => $value) {
                array_push($current_options, "{$key}={$value}");
            }
            
            $string_options = implode('&', $current_options);
            $url = "{$this->base_url}?{$string_options}";
            $this->XML=@simplexml_load_file($url);
            if ($this->XML) {
                return $this->getCoordinates();
            } else {
                throw new Exception("Can not get data!");
            }
        }
        
        private function getCoordinates() {
            $coordinates = array();
            $xmlCoordinates = $this->XML->itdTripRequest->itdItinerary->itdRouteList->itdRoute->
                    itdPartialRouteList->itdPartialRoute->itdPathCoordinates->itdCoordinateBaseElemList->
                    itdCoordinateBaseElem;
            foreach ($xmlCoordinates as $coordinate) {
                $currentCoordinate = array (
                    "long" => (float)$coordinate->x,
                    "lat" => (float)$coordinate->y
                );
                array_push($coordinates, $currentCoordinate);
            }
            
            return $coordinates;
        }
    }
?>