<?php
    date_default_timezone_set('America/Los_Angeles');

    if($_POST){

        if($_POST['action'] == 'addCreature') {

            $date = $_POST['sightings_date'];
            $type = htmlspecialchars($_POST['creature_type']);
            $distance = htmlspecialchars($_POST['creature_distance']);
		    $weight = htmlspecialchars($_POST['creature_weight']);
		    $height = htmlspecialchars($_POST['creature_height']);
		    $color = htmlspecialchars($_POST['creature_color_rgb']);
		    $lat = htmlspecialchars($_POST['sighting_latitude']);
            $long = htmlspecialchars($_POST['sighting_longitude']);
            
            $my_date = date('Y-m-d', strtotime($date));

            if($type == '') {
                $type = "Other";
            }

            $query = "INSERT INTO sightings (sightings_date, creature_type, creature_distance, creature_weight, creature_height, creature_color, sighting_latitude, sighting_longitude)";
            $query .= "VALUES ('$my_date', '$type', '$distance', '$weight', '$height', '$color', '$lat', '$long') ";

            $result = db_connection($query);

            if($result) {
                $msg = "Creature added successfully";
                success($msg);
            } else {
                fail('Insert failed.');
            }
            exit;
        } //End POST action
    } //End POST

    if($_GET){
        if($_GET['action'] == 'getAllSightings'){
            $query = "SELECT sightings_id, sightings_date, creature_type FROM sightings order by sightings_date ASC ";
            $result = db_connection($query);
            
            $sightings = array();
    
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                array_push($sightings, array('id' => $row['sightings_id'], 'date' => $row['sightings_date'], 'type' => $row['creature_type'] ));
            }
            echo json_encode(array("sightings" => $sightings));
            exit;	
        }elseif($_GET['action'] == 'getSingleSighting'){
            $id = htmlspecialchars($_GET['id']);
            $query = "SELECT sightings_date, creature_type, creature_distance, creature_weight, creature_height, creature_color, sighting_latitude, ";
            $query .= " sighting_longitude FROM sightings where sightings_id = '$id' ";
            $result = db_connection($query);
            
            $sightings = array();
    
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                array_push($sightings, array('date' => $row['sightings_date'], 'type' => $row['creature_type'], 'distance' => $row['creature_distance'], 'weight' => $row['creature_weight'],
                        'height' => $row['creature_height'], 'color' => $row['creature_color'], 'lat' => $row['sighting_latitude'], 'long' => $row['sighting_longitude']
                    )
                );
            }
            echo json_encode(array("sightings" => $sightings));
            exit;	
        }elseif($_GET['action'] == 'getSightingsByType'){
            $type = htmlspecialchars($_GET['type']);
            $query = "SELECT sightings_id, sightings_date, creature_type, creature_distance, creature_weight, creature_height, creature_color, sighting_latitude, ";
            $query .= " sighting_longitude FROM sightings where creature_type = '$type' order by sightings_date ASC ";
            $result = db_connection($query);
            $sightings = array();
    
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                array_push($sightings, array('id' => $row['sightings_id'], 'date' => $row['sightings_date'], 'type' => $row['creature_type'], 'distance' => $row['creature_distance'], 'weight' => $row['creature_weight'], 
                        'height' => $row['creature_height'], 'color' => $row['creature_color'], 'lat' => $row['sighting_latitude'], 'long' => $row['sighting_longitude']
                    )
                );
            }
            echo json_encode(array("sightings" => $sightings));
            exit;
        }elseif($_GET['action'] == 'getSightingsTypes'){
            $query = "SELECT distinct(creature_type) FROM sightings order by creature_type ASC ";
            $result = db_connection($query);
            
            $types = array();
    
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                array_push($types, array('type' => $row['creature_type']));
            }
            echo json_encode(array("creature_types" => $types));
            exit;
        }else{}
    }	

    function db_connection($query){
        $conect = mysqli_connect('ip', 'user_name', 'password', 'Data_Base')
        OR die( 'Could not connect to database.');
        
        return mysqli_query($conect, $query);
    }
	
	function fail($message) {
		die(json_encode(array('status' => 'fail', 'message' => $message)));
	}
	function success($message) {
		die(json_encode(array('status' => 'success', 'message' => $message)));
	}



?>