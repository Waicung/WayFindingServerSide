<?php
require_once __DIR__ . '/db_config.php';
$conn=mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
$response = array();
$search_query = "SELECT * FROM locations
WHERE record_id in (
SELECT record_id FROM recordings 
WHERE assignment_id in(
SELECT assignment_id FROM route_assignments 
WHERE user_id in (
SELECT user_id FROM users
WHERE user_group = 0)))
AND latitude <> 0
ORDER BY record_id,time_stamp";
$result = mysqli_query($conn,$search_query);
// TODO: check if empty
$tag = "";
$locaions = array();
$point = array();
$record = array();
while($row = mysqli_fetch_assoc($result)){
  if($tag=="" || $tag<>$row['record_id']){
    if($tag==""){}
      else{
        $record['id'] = $tag;
        $record['locations'] = $locations;
        array_push($response,$record);
      }
    $tag = $row['record_id'];
    $locations = array();
  }
  if($tag == $row['record_id']){
    $point = array();
    $point['lat'] = doubleval($row['latitude']);
    $point['lng'] = doubleval($row['longitude']);
    $point['location_id'] = intval($row['location_id']);
    array_push($locations,$point);
  }

}
$record['id'] = $tag;
$record['locations'] = $locations;
array_push($response,$record);


 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Walking Path</title>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<!--    <script type="text/javascript" src="http://livejs.com/live.js"></script>-->
    <script src="snaptoroad.js"></script>
    <style>
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      .map {
        height: 500px;
      }
      .floating-title {
        z-index: 999;
        background: #fff;
        padding: 5px;
        font-size: 14px;
        font-family: Arial;
        border: 1px solid #ccc;
        box-shadow: 0 2px 2px rgba(33, 33, 33, 0.4);
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
                <li class="active"><a href="/visualisation.php">Locations</a></li>
                <li><a href="/findings.php">Findings</a></li>
            </ul>
        </div>
    </nav>
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12" style="padding-bottom: 15px">
          <button type="button" class="btn btn-primary btn-block" id="snap-button">Snap-to-Road</button>
          <button type="button" class="btn btn-primary btn-block" id="show-button">Show snapped routes</button>
        </div>
      </div>
      <div class="row" id="map-container">
        <div class="col-sm-4 map" id="map"></div>
      </div>
    </div>

    <script>// google map script

      var center = {lat:-37.795395, lng:144.954932};
      //var center = {lat:-37.799604, lng:144.957807};
      var map;
      var records = <?php echo json_encode($response) ?>;

      function initMap(){
        map = createMap(center,"");
        initialize();
      }

      function initialize() {
        var recordslength = records.length;
        for(var i=0;i<recordslength;i++){
          drawRoute(records[i]);
        }
      }

      function drawRoute(route){
        var newRoute = splitLocationId(route,1);
        var locations = newRoute.locations;
        for(var i=0; i<locations.getLength();i++) {
          var latLng= locations.getAt(i);
          addMarker(latLng, map);
        }
        addLine(locations,map);
      }

      //create a new map view with a title name by mapId
      function createMap(center, mapId){
        if(mapId!=""){
          //$("map").remove()
          $("#map-container").append("<div class='col-sm-4'><div class='floating-title'>Test "+mapId+"</div><div class='map' id='map" + mapId +"'></div></div>");
        }
        var map = new google.maps.Map(document.getElementById("map"+mapId), {
          zoom: 17,
          center: center,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        return map;
      }

      function addLine(route,map) {
        var walkingPath = new google.maps.Polyline({
          path: route,
          strokeColor: '#FF0000',
          strokeOpacity: 1.0,
          strokeWeight: 2
        });
        walkingPath.setMap(map);
      }
      function addMarker(latLng,map) {
        var marker = new google.maps.Marker({
          position: latLng,
          map: map
        });
      }

    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAZEyaeSOnH8dcVq646GIyUQbxGKHza_dc&callback=initMap">
    </script>

    <script>//button click
      $(document).ready(function(){
        $('#snap-button').click(function() {
          var recordslength = records.length;
          for(var i=0;i<recordslength;i++){
            runSnapToRoad(splitLocationId(records[i],4));
          }

        });
        $('#show-button').click(function() {
          $.get("snappedLocation.php",
              function(data){
                var recordslength = JSON.parse(data).length;
                for(var i=0;i<recordslength;i++){
                  map = createMap(center,i.toString());
                  drawRoute(JSON.parse(data)[i]);
                }
              });
        });
      });

      function splitLocationId(route,interval){
        var locations = route.locations;
        var walkingRecords = new google.maps.MVCArray([]);
        var idList = [];
        for(var j =0; j<locations.length; j+=interval) {
          var coords = locations[j];
          var lat = coords.lat;
          var lng = coords.lng;
          var id = coords.location_id;
          var latLng =  new google.maps.LatLng(coords.lat,coords.lng);
          walkingRecords.push(latLng);
          idList.push(id);
        }
        var newRoute = new Object();
        newRoute.locations = walkingRecords;
        newRoute.ids = idList
        return newRoute;
      }



    </script>
  </body>
</html>
