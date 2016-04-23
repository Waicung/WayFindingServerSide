<?php
/**
 * Created by PhpStorm.
 * User: waicung
 * Date: 17/04/2016
 * Time: 15:38
 * Get steps based on route numbers(only return on step)
 */

require_once __DIR__ . '/db_config.php';
$conn=mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
$response = array();

if(isset($_GET['route_id'])){
    $route_id = $_GET['route_id'];
    $result = mysqli_query($conn,"SELECT * FROM steps WHERE route_id = $route_id");

    if(!empty($result)) {
        // check for empty result
        if (mysqli_num_rows($result) > 0) {
            $response['steps'] = array();
            while ($row = mysqli_fetch_array($result)) {
                $step = array();
                $step['step_number'] = $row['step_number'];
                $step['distance'] = $row['distance'];
                $step['duration'] = $row['duration'];
                $step['start_location'] = $row['start_location'];
                $step['end_location'] = $row['end_location'];
                $step['instruction'] = $row['instruction'];

                //push single row into final response array
                array_push($response['steps'], $step);
            }
        } else {
            $response["success"] = 0;
            $response["message"] = "No steps found";
        }
    }
    else {
        $response['success'] = 0;
        $response['message'] = "Required field missing";
    }
    echo json_encode($response);
}
