<?php
$testList = getTestList();
$response = array();
$routeList = getRouteList($testList);
if(isset($_POST['route_id'])){
  $response = getRoute($_POST['route_id']);
}else {
  $response = getRoute(5);
}

function getTestList(){
  require_once __DIR__ . '/db_config.php';
  $conn=mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
  $search_query = "SELECT DISTINCT test_group FROM routes";
  $result = mysqli_query($conn,$search_query);
  $response = array();
  while($row = mysqli_fetch_assoc($result)){
    array_push($response,$row['test_group']);
  }
  mysqli_close($conn);
  return $response;
}

function getRouteList($testList){
  $response = array();
  require_once __DIR__ . '/db_config.php';
  $conn=mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
  foreach ($testList as $test) {
    $search_query = "SELECT * FROM routes WHERE test_group = '$test'";
    $result = mysqli_query($conn,$search_query);
    $response[$test] = array();
    while($row = mysqli_fetch_assoc($result)){
      array_push($response[$test],$row['route_id']);
    }
  }
  mysqli_close($conn);
  return $response;

}

function getRoute($route_id){
  require_once __DIR__ . '/db_config.php';
  $conn=mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
  $search_query = "Select  w1.latitude as start_lat,w1.longitude as start_lng,w2.latitude as end_lat,w2.longitude as end_lng from steps
  left join way_points w1 on steps.start_location=w1.point_id
  left join way_points w2 on steps.end_location=w2.point_id
  where steps.route_id = $route_id";
  $result = mysqli_query($conn,$search_query);
  $response = array();
  $point1 = array();
  $point2 = array();
  while($row = mysqli_fetch_assoc($result)){
    $point1['lat'] = doubleval($row['start_lat']);
    $point1['lng'] = doubleval($row['start_lng']);
    $point2['lat'] = doubleval($row['end_lat']);
    $point2['lng'] = doubleval($row['end_lng']);
    array_push($response,$point1);
    array_push($response,$point2);
  }
  mysqli_close($conn);
  return $response;
}

function setGroupOption($group){
  foreach( $group as $item){
    echo "<option value='",$item,"'>",$item,"</option>";
  }
}
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
    <script>
      var routeList = <?php echo json_encode($routeList);?>;
      $(document).ready(function () {
          $("#test_group").change(function () {
            $("option").remove(".wayfinding-option");
            var val = $(this).val();
            var option = routeList[val];
            for(var i=0; i<option.length; i++){
              var route = option[i];
              $("#route").prepend("<option class='wayfinding-option' value='"+route+"'>"+route+"</option>");
              //$("#route").prepend("<b>Prepended text</b>");
            }
          });
      });
    </script>
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
                <li class="active"><a href="/visualstep.php">Routes</a></li>
                <li><a href="/visualisation.php">Locations</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">
      <form role="form" action="visualstep.php" method="post">
      <div class="form-group">
          <div class="row">
              <div class="col-xs-3">
                  <label for="test_group">Test Group</label>
                  <select id="test_group" class="form-control">
                      <?php setGroupOption($testList);?>
                  </select>
              </div>
              <div class="col-xs-3">
                  <label for="route">Route</label>
                  <select id="route" class="form-control" name="route_id">
                      <option class="wayfinding-option">Select a Test</option>
                  </select>
              </div>
          </div>
      </div>
      <button type="submit" class="btn btn-default">Submit</button>
  </form>
    </div>
    <div id="map"></div>
    <script>

      // This example adds a UI control allowing users to remove the polyline from the
      // map.

      var route;
      var stepNotes;
      var map;
      //-37.793717, 144.923645
      //var center = {lat:-37.799604, lng:144.957807};
      var records = <?php echo json_encode($response) ?>;
      var center_lat = records[0].lat;
      var center_lng = records[0].lng;
      var center = {lat:center_lat, lng:center_lng};

      function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 15,
          center: center,
          //mapTypeId: google.maps.MapTypeId.TERRAIN
        });
        var stepNotes = new google.maps.MVCArray([]);
        for(var i = 0; i<records.length; i++){
            var coords = records[i];
            var lat = coords.lat;
            var lng = coords.lng;
            if(lat!=0){

              var latLng =  new google.maps.LatLng(coords.lat,coords.lng);
              stepNotes.push(latLng);
              var marker = new google.maps.Marker({
                position: latLng,
                map: map
              });
            }
          }
          route = new google.maps.Polyline({
            path: stepNotes,
            strokeColor: '#FF0000',
            strokeOpacity: 1.0,
            strokeWeight: 2
          });
          addLine();
        }


      function addLine() {
        route.setMap(map);
      }

    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAZEyaeSOnH8dcVq646GIyUQbxGKHza_dc&callback=initMap">
    </script>
  </body>
</html>
