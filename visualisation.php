<?php
require_once __DIR__ . '/db_config.php';
$conn=mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
$response = array();
$search_query = "SELECT * FROM locations";
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
    array_push($locations,$point);
  }

}
$record['id'] = $tag;
$record['locations'] = $locations;
array_push($response,$record);

  // $point = array();
  // $point['lat'] = doubleval($row['latitude']);
  // $point['lng'] = doubleval($row['longitude']);
  // array_push($response,$point);

//echo json_encode($response);

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
    <style>
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #map {
        height: 100%;
      }
      #floating-panel {
        position: absolute;
        top: 10px;
        left: 25%;
        z-index: 5;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #999;
        text-align: center;
        font-family: 'Roboto','sans-serif';
        line-height: 30px;
        padding-left: 10px;
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
            </ul>
        </div>
    </nav>
    <div id="map"></div>



    <script>// google map script
      var flightPath;
      var walkPath;
      var map;
      var center = {lat:-37.799604, lng:144.957807};

      function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 15,
          center: center,
          //mapTypeId: google.maps.MapTypeId.TERRAIN
        });

        var records = <?php echo json_encode($response) ?>;
        for(var i = 0; i<records.length; i++){
          var record = records[i];
          var locations = record.locations;
          var walkingRecords = new google.maps.MVCArray([]);
          for(var j =0; j<locations.length; j+=10){
            var coords = locations[j];
            var lat = coords.lat;
            var lng = coords.lng;
            if(lat!=0){
            var latLng =  new google.maps.LatLng(coords.lat,coords.lng);
            walkingRecords.push(latLng);
            var marker = new google.maps.Marker({
              position: latLng,
              map: map
            });
          }
        }
          walkingPath = new google.maps.Polyline({
            path: walkingRecords,
            strokeColor: '#FF0000',
            strokeOpacity: 1.0,
            strokeWeight: 2
          });
          addLine();
        }

      }

      function addLine() {
        walkingPath.setMap(map);
      }

      function removeLine() {
        flightPath.setMap(null);
      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAZEyaeSOnH8dcVq646GIyUQbxGKHza_dc&callback=initMap">
    </script>
  </body>
</html>
