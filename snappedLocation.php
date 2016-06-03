<?php
/**
 * Created by PhpStorm.
 * User: waicung
 * Date: 01/06/2016
 * Time: 20:27
 */

require_once __DIR__ . '/db_config.php';
$conn=mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
$response = array();
$search_query = "SELECT l.record_id AS record_id,l.location_id,s.latitude,s.longitude FROM locations AS l
JOIN snap_to_road AS s ON l.location_id=s.matchedlocation_id
WHERE l.record_id in (
SELECT l.record_id FROM recordings 
WHERE assignment_id in(
SELECT assignment_id FROM route_assignments 
WHERE user_id in (
SELECT user_id FROM users
WHERE user_group = 0)))
AND s.latitude <> 0
ORDER BY l.record_id,time_stamp";
$result = mysqli_query($conn,$search_query);
// TODO: check if empty
$tag = "";
$locaions = array();
$point = array();
$record = array();
while($row = mysqli_fetch_assoc($result)){
    if($tag=="" || $tag<>$row['record_id']){
        if($tag==""){}
        else{
            $record['id'] = $tag;
            $record['locations'] = $locations;
            array_push($response,$record);
        }
        $tag = $row['record_id'];
        $locations = array();
    }
    if($tag == $row['record_id']){
        $point = array();
        $point['lat'] = doubleval($row['latitude']);
        $point['lng'] = doubleval($row['longitude']);
        $point['location_id'] = intval($row['location_id']);
        array_push($locations,$point);
    }

}
$record['id'] = $tag;
$record['locations'] = $locations;
array_push($response,$record);

echo json_encode($response);
?>