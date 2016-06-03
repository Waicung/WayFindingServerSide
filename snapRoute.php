<?php
require_once __DIR__ . '/db_config.php';
$conn=mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
$response = array();
array_push($response,"response");

if(isset($_POST['data'])){
    $data = $_POST['data'];
    $counter = 0;
    foreach($data as $row){
        $counter+=1;
        $lat = doubleval($row['lat']);
        $lng = doubleval($row['lng']);
        $id = intval($row['location_id']);
        $insert_query = "INSERT INTO snap_to_road (matchedlocation_id,latitude,longitude) VALUES ($id,$lat,$lng)";
        mysqli_query($conn,$insert_query);
        //array_push($response,$lat,$lng,$id);
    }
    //array_push($response,$counter);
    //echo json_encode($response);
}


?>