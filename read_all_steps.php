<?php
/**
 * Created by PhpStorm.
 * User: waicung
 * Date: 17/04/2016
 * Time: 16:00
 * read all the row in a table
 */

require_once __DIR__ . '/db_config.php';
$conn=mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

$response = array();

$result = mysqli_query($conn, "SELECT * FROM steps");

if (mysqli_num_rows($result) > 0){
    //looping through results
    $response['steps'] = array();
    while ($row = mysqli_fetch_array($result)){
        $step = array();
        $step['step_number'] = $row['step_number'];
        $step['distance'] = $row['distance'];
        $step['duration'] = $row['duration'];
        $step['start_location'] = $row['start_location'];
        $step['end_location'] = $row['end_location'];
        $step['instruction'] = $row['instruction'];

        //push single row into final response array
        array_push($response['steps'],$step);
    }
    $response['success'] = 1;
}
else{
    $response['success'] = 0;
    $response['message'] = "No result";
}

echo json_encode($response);
