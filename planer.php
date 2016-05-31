<?php
if (isset($_POST['username'])&&
    isset($_POST['password'])&&
    isset($_POST['user_group'])){
// TODO: input is not promising

    require_once __DIR__ . '/db_config.php';
    require_once __DIR__ . '/pointsUpdater.php';
    $conn=mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
    $start_lat = doubleval($_POST['start_lat']);
    $start_lng = doubleval($_POST['start_lng']);
    $start_name = $_POST['start_name'];
    $end_lat = doubleval($_POST['destination_lat']);
    $end_lng = doubleval($_POST['destination_lng']);
    $end_name = $_POST['destination_name'];
    $pointUpdator = new pointsUpdater();
    $start_id = $pointUpdator->updatePoint($conn,$start_lat,$start_lng,$start_name);
    $end_id = $pointUpdator->updatePoint($conn,$end_lat,$end_lng,$end_name);
    $username = $_POST['username'];
    $password = $_POST['password'];
    $group = $_POST['user_group'];
    $user_id = newUser($conn,$username,$password,$group);
    $route_id = newRoute($conn, $start_id, $end_id, $group);
    $assignment_id = newAssignment($conn, $user_id, $route_id);
}

function newUser($conn,$username,$password,$group){
  $search_query = "SELECT * FROM users WHERE username = '$username'";
  $search_result = mysqli_query($conn,$search_query);
  if(!empty($search_result)&&mysqli_num_rows($search_result)>0){
      $user = mysqli_fetch_array($search_result);
      //if exist, return point_id
      $user_id = $user['user_id'];
      return $user_id;
  }else{
    $insert_query = "INSERT INTO users (username, password, user_group) VALUES
    ('$username','$password','$user_group')";
    mysqli_query($conn,$insert_query);
    return newUser($conn,$username,$password,$group);
  }
}

function newRoute($conn,$start_id,$end_id,$group){
  $search_query = "SELECT * FROM routes WHERE start_point = $start_id AND end_point = $end_id";
  $search_result = mysqli_query($conn, $search_query);
  if(!empty($search_result)&&mysqli_num_rows($search_result)>0){
      $route = mysqli_fetch_array($search_result);
      //if exist, return point_id
      $route_id = $route['route_id'];
      return $route_id;
  }else{
    echo $start_id,$end_id,$group;
    $insert_query = "INSERT INTO routes (start_point, end_point, test_group) VALUES
    ($start_id,$end_id,'$group')";
    mysqli_query($conn,$insert_query);
    return newRoute($conn,$start_id,$end_id,$group);
  }
}

  function newAssignment($conn,$user_id,$route_id){
    $search_query = "SELECT * FROM route_assignments WHERE user_id = $user_id AND route_id = $route_id";
    $search_result = mysqli_query($conn,$search_query);
    if(!empty($search_result)&&mysqli_num_rows($search_result)>0){
        $assignment = mysqli_fetch_array($search_result);
        //if exist, return point_id
        $assignment_id = $assignment['assignment_id'];
        return $assignment_id;
    }else{
      $insert_query = "INSERT INTO route_assignments (user_id, route_id) VALUES
      ($user_id,$route_id)";
      mysqli_query($conn,$insert_query);
      return newAssignment($conn,$user_id,$route_id);
    }
  }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Wayfinding</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <!--autoreload-->
    <script type="text/javascript" src="http://livejs.com/live.js"></script>
</head>

<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">Wayfinding</a>
        </div>
        <ul class="nav navbar-nav">
            <li><a href="/index.php">Home</a></li>
            <li class="active"><a href="/planer.php">Route Planer</a></li>
            <li><a href="/visualstep.php">Routes</a></li>
            <li><a href="/visualisation.php">Locations</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h3>Route Planer</h3>
    <div class="row"><?php if($assignment_id<>null){echo '<span class="label label-success">Success</span>';}?></div>
    <form role="form" method="post" action="planer.php">
        <div class="form-group">
            <div class="row">
                <div class="col-xs-2">
                    <label for="start">START</label>
                    <span class="glyphicon glyphicon glyphicon-map-marker" id="start"></span>
                </div>
                <div class="col-xs-3">
                    <label for="start_lat">Latitude</label>
                    <input class="form-control" type="text" id="start_lat" name="start_lat">
                </div>
                <div class="col-xs-3">
                    <label for="start_lng">Longitude</label>
                    <input class="form-control" type="text" id="start_lng" name="start_lng">
                </div>
                <div class="col-xs-3">
                    <label for="start_name">Name</label>
                    <input class="form-control" type="text" id="start_name" name="start_name">
                </div>
            </div>

            <div class="row">
                <div class="col-xs-2">
                    <label for="destination">DESTINATION</label>
                    <span class="glyphicon glyphicon glyphicon-map-marker" id="destination"></span>
                </div>
                <div class="col-xs-3">
                    <label for="destination_lat">Latitude</label>
                    <input class="form-control" type="text" id="destination_lat" name="destination_lat">
                </div>
                <div class="col-xs-3">
                    <label for="destination_lng">Longitude</label>
                    <input class="form-control" type="text" id="destination_lng" name="destination_lng">
                </div>
                <div class="col-xs-3">
                    <label for="destination_name">Name</label>
                    <input class="form-control" type="text" id="destination_name" name="destination_name">
                </div>
            </div>

            <div class="row">
                <div class="col-xs-2">
                    <label for="user">PARTICIPANT</label>
                    <span class="glyphicon glyphicon-user" id="user"></span>
                </div>
                <div class="col-xs-3">
                    <label for="tester_name">Name</label>
                    <input class="form-control" type="text" id="user_name" name="username">
                </div>
                <div class="col-xs-3">
                    <label for="tester_psd">Password</label>
                    <input class="form-control" type="text" id="user_psd" name="password">
                </div>
                <div class="col-xs-3">
                    <label for="tester_group">Group</label>
                    <input class="form-control" type="text" id="user_group" name="user_group">
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
    </form>

</div>

</body>
</html>
