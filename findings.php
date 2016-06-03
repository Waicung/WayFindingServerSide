<?php
/**
 * Created by PhpStorm.
 * User: waicung
 * Date: 02/06/2016
 * Time: 15:07
 */

require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/myfunctions.php';
$conn=mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
$response = array();
$search_query = "SELECT l.location_id,l.record_id,latitude,longitude,time_stamp,a.route_id
FROM locations AS l
LEFT JOIN recordings AS r
ON l.record_id = r.record_id
LEFT JOIN route_assignments AS a
ON r.assignment_id=a.assignment_id
WHERE l.record_id>15
AND latitude<>0
ORDER BY route_id,record_id, time_stamp";
$result = mysqli_query($conn,$search_query);

$route = "";
$tag = ""; // mark the current record_id
$locaions = array();
$point = array();
$record = array();
$speedResponse = array();
$speeds = array();
while($row = mysqli_fetch_assoc($result)){
  if($tag=="" || $tag<>$row['record_id']){
    if($tag==""){}
    else{
      $record['route_id'] = $route;
      $record['id'] = $tag;
      $record['locations'] = $locations;
      array_push($response,$record);
      $speeds['route_id'] = $route;
      $speeds['record_id'] = $tag;
      $speeds['speed'] = array();
      array_push($speeds['speed'],initialSpeedComputation($locations));
      array_push($speedResponse,$speeds);
    }
    $route = intval($row['route_id']);
    $tag = $row['record_id'];
    $locations = array();
  }
  if($tag == $row['record_id']){
    $point = array();
    $point['lat'] = doubleval($row['latitude']);
    $point['lng'] = doubleval($row['longitude']);
    $point['location_id'] = intval($row['location_id']);
    $point['time_stamp'] = $row['time_stamp'];
    array_push($locations,$point);
  }

}
$record['route_id'] = $route;
$record['id'] = $tag;
$record['locations'] = $locations;
array_push($response,$record);
$speeds['route_id'] = $route;
$speeds['record_id'] = $tag;
$speeds['speed'] = array();
array_push($speeds['speed'],initialSpeedComputation($locations));
array_push($speedResponse,$speeds);

?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Walking Path</title>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
      <link rel="stylesheet" href="/chartistjs/dist/chartist.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
      <script src="/chartistjs/dist/chartist.min.js"></script>
    <script src="https://npmcdn.com/simple-statistics@2.0.0/dist/simple-statistics.min.js"></script>
    <style>
      .ct-chart{height: auto;
      }
    </style>

</head>
<body>
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">Wayfinding</a>
    </div>
    <ul class="nav navbar-nav">
      <li><a href="/index.php">Home</a></li>
      <li><a href="/planer.php">Route Planer</a></li>
      <li><a href="/visualstep.php">Routes</a></li>
      <li><a href="/visualisation.php">Locations</a></li>
      <li class="active"><a href="/findings.php">Findings</a></li>
    </ul>
  </div>
</nav>

<div class="container-fluid">
  <div class="row">
    <div class="row">
      <div class="col-sm-12" id="chart-container">
        
      </div>

    </div>


    <script src="/findings.js"></script>
    <script>
      var records = <?php echo json_encode($response);?>;
      var speeds = <?php echo json_encode($speedResponse);?>;
      var routeList = []; // number of routes
      var currentRoute = records[0].route_id;
      routeList.push(currentRoute);
      for(var i = 1;i<records.length; i++){
        if(currentRoute != records[i].route_id){
          currentRoute = records[i].route_id;
          routeList.push(currentRoute);
        }
      }
      for(var i=0;i<routeList.length;i++){
        var speedLists = [];
        var currentRoute = routeList[i];
        for(var j = 0;j<speeds.length;j++){
          if(currentRoute == speeds[j].route_id){
            var currentTest = speeds[j].record_id;
            var eachList = [];
            eachList.push(getSpeeds(speeds[j].speed[0]));
            addSpeedChart("Route"+currentRoute+"Record"+currentTest, eachList);
            speedLists.push(getSpeeds(speeds[j].speed[0]));
          }
        }
        addSpeedChart("Route"+currentRoute, speedLists);
        
      }

      function addSpeedChart(title,speedLists){
        addCol(title);
        speedChart(title,speedLists);
      }

      function getSpeeds(speedList){
        var output = [];
        for(var i=0;i<speedList.length;i++){
          output.push(speedList[i].speed);
        }
        return output;
      }
      
      function addCol(title){
        $("#chart-container").append(
            "<div class='ct-chart .ct-minor-sixth' id="+title+">" +
            "<div class='floating-title'>"+title+"</div></div>");
      }

      function averageSpeed(speeds){
        var output = 0;
        var lowQuantile = quantile(speeds, 0.25);
        var highQuantile = quantile(speeds, 0.75);
        for(var i=0; i<speeds.length;i++){
          if(speeds[i]<=lowQuantile || speeds[i]>=highQuantile){
            speeds.splice(i, 1);
          }
        }
        output = mean(speeds);
        return output;
      }

    </script>
  </div>
</div>
</body>
</html>