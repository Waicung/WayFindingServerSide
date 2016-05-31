<?php
/**
 * Created by PhpStorm.
 * User: waicung
 * Date: 28/04/2016
 * Time: 18:02
 */

require_once __DIR__ . '/db_config.php';
$conn=mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
$response = array();
date_default_timezone_set('UTC');

if (isset($_POST['locations'])&&
    isset($_POST['assignment_id'])){
    $assignment_id = $_POST['assignment_id'];
    $record_id = createRecord($conn,$assignment_id);

    if ($record_id <> null && $record_id<>0) {
        $locations = json_decode($_POST['locations'], true);
        foreach ($locations as $location) {
            insertLocation($conn, $record_id, $location);
        }
        $response['success'] = true;
        $response['message'] = "successfully upload data";

    }
    else{
        $response['success'] = false;
        switch ($record_id){
            case 0:
                $response['message'] = "record already exist";
                break;
            case null:
                $response['message'] = "error";
        }

    }
}
else {
    $response['success'] = false;
    $response['message'] = "error";
}
echo json_encode($response);

function insertLocation($conn, $record_id, $location){
    $dFormat = "Y-m-d h:i:s";
    if (!empty($location)){
        $lat = $location['lat'];
        $lng = $location['lng'];
        $time_stamp = $location['time'];
        $time = date($dFormat,$time_stamp);
        $step_number = $location['step_number'];
        $event = $location['event'];
        $insert_query = "INSERT INTO locations (record_id, latitude, longitude, time_stamp, step, event) VALUES ($record_id, $lat,$lng,'$time',$step_number, '$event')";
        mysqli_query($conn, $insert_query);
    }
}

function createRecord($conn,$assignment_id){
    if (!empty($assignment_id)){
        $search_query = "SELECT record_id FROM recordings WHERE assignment_id = $assignment_id";
        //check if the result has already been recorded
        $exist = mysqli_query($conn,$search_query);
        if(!empty($exist)&&mysqli_num_rows($exist)>0){
            return 0;
        }
        else {
            $inset_query = "INSERT INTO recordings (assignment_id) VALUE ($assignment_id)";
            mysqli_query($conn, $inset_query);
            $result = mysqli_query($conn, $search_query);
            if (!empty($result) && mysqli_num_rows($result) > 0) {
                $record = mysqli_fetch_array($result);
                $record_id = $record['record_id'];
                return $record_id;
            }
        }
    }
    return null;
}
