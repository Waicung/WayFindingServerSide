<?php
/**
 * Created by PhpStorm.
 * User: waicung
 * Date: 17/04/2016
 * Time: 15:03
 * Following code will create a new route row
 * Route details are read from http Post Request
 */

// array for JSON response
$response = array();

//check for required fields
if (isset($_POST['route_id']) &&
    isset($_POST['step_number']) &&
    isset($_POST['distance']) &&
    isset($_POST['duration']) &&
    isset($_POST['start_location']) &&
    isset($_POST['end_location']) &&
    isset($_POST['instruction'])) {

    $route_id = $_POST['route_id'];
    $step_number = $_POST['step_number'];
    $distance = $_POST['distance'];
    $duration = $_POST['duration'];
    $start_location = $_POST['start_location'];
    $end_location = $_POST['end_location'];
    $instruction = $_POST['instruction'];

    //connection to db
    require_once __DIR__ . '/db_connect.php';
    $db = new DB_CONNECT();

    //inserting new row
    $result = mysqli_query("INSERT INTO steps(route_id, step_number, distance, duration, start_location,
        end_location, instruction) VALUES('$route_id','$step_number', '$distance', '$duration', '$start_location',
        '$end_location', '$instruction')");

    //check if row inserted 
    if ($result) {
        //successfully inserted
        $response["success"] = 1;
        $response["message"] = "Step successfully created.";
    } else {
        //failed to insert
        $response['success'] = 0;
        $response['message'] = "An error occurred";
    }
}
else {
    //missing field
    $response['success'] = 0;
    $response['message'] = "Required field is missing";
}
//echoing JSON response
echo json_encode($response);
?>