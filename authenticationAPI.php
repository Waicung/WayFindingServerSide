<?php
/**
 * Created by PhpStorm.
 * User: waicung
 * Date: 19/04/2016
 * Time: 13:10
 */

require_once __DIR__ . '/db_config.php';
$conn=mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
//code: have assigned route; steps_info; if modified: if tested
$CODE_NO_ROUTE = 0000; //just assigned
$CODE_NO_STEP = 1000;
$CODE_NOT_MODIFIED = 1100; //not yet modified
$CODE_NOT_TESTED = 1110;  //not yet tested
$CODE_ALL_TESTED = 1111; //all routes were tested


$response = array();

if (isset($_POST['username'])&&
    isset($_POST['password'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    //search for the user
    $result = mysqli_query($conn,"SELECT * FROM users WHERE username = '$username' and password = '$password'");

    if(!empty($result)) {
        // check if the user exist
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            $user_id = $row['user_id'];
            $response["user_id"] = $user_id;
            $response["success"] = true;
            $response["message"] = "Correct credential";
            //check if the assignment exist
            $assignments_query = "SELECT route_id FROM route_assignments WHERE user_id = $user_id";
            $assignments_search = mysqli_query($conn,$assignments_query);
            if(!empty($assignments_search && mysqli_num_rows($assignments_search)>0)) {
                //check if route has been tested(no recording)
                $assignment_query = "SELECT * FROM route_assignments WHERE user_id = $user_id AND NOT EXISTS (SELECT * FROM recordings WHERE recordings.assignment_id = route_assignments.assignment_id)";
                $assignment_search = mysqli_query($conn, $assignment_query);
                if (!empty($assignment_search && mysqli_num_rows($assignment_search) > 0)) {
                    $assignment = mysqli_fetch_array($assignment_search);
                    //not tested route_id
                    $route_id = $assignment['route_id'];
                    $modified_step = $assignment['error_step'];
                    $modified_value = $assignment['error_value'];
                    $response["route_id"] =$route_id;
                    //check if steps exists for the not tested route
                    $steps_query = "SELECT * FROM steps WHERE route_id = '$route_id' ORDER BY step_number";
                    $steps_search = mysqli_query($conn,$steps_query);
                    if(!empty($steps_search && mysqli_num_rows($steps_search)>0)) {
                        //check if route has been modified(have error_step)
                        if ($modified_step <> null) {
                            $steps = resultToArray($steps_search,$modified_step,$modified_value);
                            $response["status"] = $CODE_NOT_TESTED;
                            $response['route_id'] = $route_id;
                            $response['steps'] = $steps;

                            /*if(!empty($route_search)){
                                if(mysqli_num_rows($route_search)>0){
                                    $route = mysqli_fetch_array($route_search);
                                    $response['route_id'] = $route['route_id'];
                                    //check if the route has step info
                                    $step_query = "SELECT * FROM steps WHERE route_id = '$route_id'";
                                    $step_search = mysqli_query($conn,$step_query);
                                    if(!empty($step_search)&&mysqli_num_rows($route_search)>0){
                                        $step_code = "1";
                                    }

                                    //check if the route has been modified
                                    $step_query = "SELECT * FROM route_assignments WHERE route_id = '$route_id'";
                                    $step_search = mysqli_query($conn,$step_query);
                                    if(!empty($step_search)&&mysqli_num_rows($route_search)>0){
                                        $step_code = "1";
                                    }

                                    //get start_point coordinate
                                    $start_search = mysqli_query($conn, "SELECT * FROM way_points WHERE location_id = $start_id");
                                    $point = array();
                                    $point_coordinate  = mysqli_fetch_array($start_search);
                                    $point['lng'] = floatval ($point_coordinate ['longitude']);
                                    $point['lat'] = floatval ($point_coordinate ['latitude']);
                                    $response['points']=array();

                                    //push single row into final response array
                                    array_push($response['points'], $point);

                                    //get end_point coordinate
                                    $end_search = mysqli_query($conn, "SELECT * FROM way_points WHERE location_id = $end_id");
                                    $point = array();
                                    $point_coordinate = mysqli_fetch_array($end_search);
                                    $point['lng'] = floatval ($point_coordinate ['longitude']);
                                    $point['lat'] = floatval ($point_coordinate ['latitude']);

                                    //push single row into final response array
                                    array_push($response['points'], $point);
                                }
                            }*/
                        } else {
                            $response["status"] = $CODE_NOT_MODIFIED;
                        }
                    }
                    else{
                        $response['status'] = $CODE_NO_STEP;
                        //get start and end point
                        $route_query = "SELECT start_point,end_point FROM routes WHERE route_id = '$route_id'";
                        $route_search = mysqli_query($conn, $route_query);
                        $route = mysqli_fetch_array($route_search);
                        $start_id = $route['start_point'];
                        $end_id = $route['end_point'];
                        //get start_point coordinate
                        $start_search = mysqli_query($conn, "SELECT * FROM way_points WHERE location_id = $start_id");
                        $point = array();
                        $point_coordinate  = mysqli_fetch_array($start_search);
                        $point['lat'] = floatval ($point_coordinate ['latitude']);
                        $point['lng'] = floatval ($point_coordinate ['longitude']);
                        $response['points']=array();

                        //push single row into final response array
                        array_push($response['points'], $point);

                        //get end_point coordinate
                        $end_search = mysqli_query($conn, "SELECT * FROM way_points WHERE location_id = $end_id");
                        $point = array();
                        $point_coordinate = mysqli_fetch_array($end_search);
                        $point['lat'] = floatval ($point_coordinate ['latitude']);
                        $point['lng'] = floatval ($point_coordinate ['longitude']);


                        array_push($response['points'], $point);

                    }
                } else {
                    $response["status"] = $CODE_ALL_TESTED;
                }
            }
            else{
                $response['status'] = $CODE_NO_ROUTE;
            }
        }
        else {
            $response["success"] = false;
            $response["message"] = "Incorrect username or password";

        }
    }
}
else {
    $response['success'] = false;
    $response['message'] = "Required field missing";
}

echo json_encode($response);

function resultToArray($result,$error_step, $error_value)
{
    $rows = array();
    while ($row = mysqli_fetch_assoc($result)) {
        if($row['step_number'] == $error_step){
            $row['instruction'] = $error_value;
        }
        $rows[] = $row;
    }
    return $rows;
}