<?php
function createTable(){
  require_once __DIR__ . '/db_config.php';
  $conn=mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
  $response = array();
  $search_query = "SELECT u.username,w1.name AS start_point,w2.name AS end_point
  FROM route_assignments AS a
  JOIN users AS u ON a.user_id=u.user_id
  JOIN routes AS r ON a.route_id=r.route_id
  JOIN way_points AS w1 ON r.start_point=w1.point_id
  JOIN way_points AS w2 ON r.end_point=w2.point_id
  WHERE assignment_id IN (
  	SELECT assignment_id FROM recordings)
  AND user_group < 100";
  $result = mysqli_query($conn,$search_query);
  $record = array();
  $count = 1;
  while($row = mysqli_fetch_assoc($result)){
    $record['username'] = $row['username'];
    $record['start_point'] = $row['start_point'];
    $record['end_point'] = $row['end_point'];
    array_push($response,$record);
    echo "<tr>
        <td>",$count,"</td>
        <td>",$record['username'],"</td>
        <td>",$row['start_point'],"</td>
        <td>",$record['end_point'],"</td>
        <td>0</td>
        <td>unknown</td>
        </tr>";
    $count+=1;
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
            <li class="active"><a href="#">Home</a></li>
            <li><a href="/planer.php">Route Planer</a></li>
            <li><a href="/visualstep.php">Routes</a></li>
            <li><a href="/visualisation.php">Locations</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h3>Experiment record</h3>
    <table class="table">
        <thead>
        <tr>
            <th>#</th>
            <th>Username</th>
            <th>Start_point</th>
            <th>End_point</th>
            <th>Time</th>
            <th>Result</th>
        </tr>
        </thead>
        <tbody>
        <?php createTable()?>
        </tbody>
    </table>

</div>

</body>
</html>
