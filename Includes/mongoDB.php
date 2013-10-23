<?php
    class MyMONGODB {

        private $client;
        private $db;
        private $db_name;
        private $indexes;

        public function __construct() {
           $connection_string = getenv("MONGOLAB_URI");
           $this->db_name = getenv("MONGOLAB_DB");
           $this->client = new MongoClient($connection_string);
           $this->db = $this->client->selectDB($this->db_name);
        }
        
        private function getData($collection_name, $query = array(), $sort = array("_id" => 1)){
            $collection = $this->client->selectCollection($this->db_name, $collection_name);
            $cursor = $collection->find($query);
            return iterator_to_array($cursor->sort($sort));
        }
        
        private function saveData($data, $collection_name) {
            $collection = $this->client->selectCollection($this->db_name, $collection_name);
            $collection->insert($data);
        }
        
        private function upsertData($collection_name, $item, $criteria) {
            $collection = $this->client->selectCollection($this->db_name, $collection_name);
            $collection->update($criteria, $item, array("upsert" => true));
        }
        
        private function checkIfIndexExists($indexName, $collection) {
            foreach ($this->indexes as $index) {
                if ($index["name"] == $indexName && 
                    $index["ns"] == ($this->db_name . "." . $collection)) {
                    return true;
                }
            }
            
            return false;
        }
        
        private function searchGeoNear($collection, $type, $coordinates, $limit, $query = array()) {
            return $this->db->command(
                    array(
                        "geoNear" => $collection, 
                        "near" => array(
                            "type" => $type, 
                            "coordinates" => $coordinates
                            ), 
                        "limit" => $limit,
                        "query" => $query,
                        "spherical" => true
                        )
                    );
        }
        
        public function saveStations($stations)
        {
            foreach ($stations as $station) {
                $criteria = array("stationId" => $station["stationId"]);
                $this->upsertData("stations", $station, $criteria);
            }
            
            $collection = $this->client->selectCollection($this->db_name, "stations");
            $this->indexes = $collection->getIndexInfo();
            if(!$this->checkIfIndexExists("location_2dsphere", "stations")) {
                $collection->ensureIndex(array("location" => "2dsphere"));
            }
            
            if(!$this->checkIfIndexExists("stationId_1", "stations")) {
                $collection->ensureIndex(array("stationId" => 1));
            }
        }
        
        public function getStations($long, $lat, $max_stations) {
            $query = array("installed" => "true", "locked" => "false");
            $sort = array("name" => 1);
            $coordinates = array((float)$long, (float)$lat);
            if ($max_stations > 0) {
                return $this->searchGeoNear("stations", "Point", $coordinates, $max_stations, $query);
            }
            
            return $this->getData("stations", $query, $sort);
        }
        
        public function getStationById($id) {
            $query = array("stationId" => (int)$id);
            return $this->getData("stations", $query);
        }
        
        public function saveErrorReport($data) {
            $data = json_decode($data);
            $this->saveData($data, "errorReports");
        }
    }
?>