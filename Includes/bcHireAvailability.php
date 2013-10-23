<?php
    class BCHIRE {
        
        private $base_url;
        private $XML;
        private $stations;
        public $time;

        public function __construct() {
           $this->base_url = "http://www.tfl.gov.uk/tfl/syndication/feeds/cycle-hire/livecyclehireupdates.xml";
           $this->stations = array();
           $this->XML=@simplexml_load_file($this->base_url);
            if ($this->XML) {
                $this->time = (string)$this->XML["lastUpdate"];
                foreach ($this->XML->station as $station) {
                    $newStation = $this->createNewStation($station);
                    array_push($this->stations, $newStation);
                }
            } else {
                throw new Exception("Can not get data!");
            }
        }
        
        private function createNewStation($station) {
            $newStation = array(
                "stationId" => (int)$station->id,
                "name" => (string)$station->name,
                "installed" => (string)$station->installed,
                "locked" => (string)$station->locked,
                "freeBikes" => (string)$station->nbBikes,
                "freeDocks" => (string)$station->nbEmptyDocks,
                "docks" => (string)$station->nbDocks,
                "location" => array(
                    "type" => "Point",
                    "coordinates" => array((float)$station->long, (float)$station->lat))
            );
            return $newStation;
        }
        
        public function getData($json = false) {
            if ($json) {
                return json_encode($this->stations);
            } else {
                return $this->stations;
            }
        }
    }
?>