<?php
/**
 * Created by PhpStorm.
 * User: waicung
 * Date: 24/04/2016
 * Time: 00:14
 * a class for receiving route object and save it to database accordingly
 */
require_once __DIR__ . '/pointsUpdater.php';
require_once __DIR__ . '/stepsCreater.php';
require_once __DIR__ . '/db_config.php';
$conn=mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);$response = array();

if (isset($_POST['route'])&&
isset($_POST['route_id'])){
    $response['route'] = $_POST['route'];
    $route_id = $_POST['route_id'];
    $route = json_decode($_POST['route'],true);
    $distance = $route['distance'];
    $duration = $route['duration'];
    $start_point = $route['start_point'];
    $start_lat = $start_point['lat'];
    $start_lng = $start_point['lng'];
    $end_point = $route['end_point'];
    $end_lat = $end_point['lat'];
    $end_lng = $end_point['lng'];
    //update way_points get point_id
    $pointUpdator = new pointsUpdater();
    $empty_name = "";
    $start_point_id = $pointUpdator->updatePoint($conn, $start_lat,$start_lng,$empty_name);
    $end_point_id = $pointUpdator->updatePoint($conn,$end_lat,$end_lng,$empty_name);
    //update route
    $update_query = "UPDATE routes SET distance = $distance, duration = $duration, origin = $start_point_id, destination = $end_point_id WHERE route_id = $route_id";
    mysqli_query($conn, $update_query);
    //Create steps
    $steps = $route['steps'];
    $stepCteater = new stepsCreater();
    $result = $stepCteater->createStep($conn,$route_id,$steps);    $response['StepResult'] = array();    array_push($response['StepResult'],$result);


}
