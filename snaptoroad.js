/**
 * Created by waicung on 01/06/2016.
 */

var snappedCoordinates = [];
var apiKey = 'AIzaSyAZEyaeSOnH8dcVq646GIyUQbxGKHza_dc';
var counter;

// Snap a user-created polyline to roads and save the path
function runSnapToRoad(route) {
    var pathLength=route.locations.getLength();
    if(pathLength>100){
        var newRoute = breakLongPath(route);
        for(var i=0;i<2;i++) {
            var segment = new Object();
            segment.locations = newRoute.locations[i];
            segment.ids = newRoute.ids[i];
            requestSnap(segment);
        }
    }
    else{
        requestSnap(route);
    }
}

function breakLongPath(route){
    var i = 1; // the factors of 100
    var segment = [];
    var subIdList = [];
    var newRoute = new Object();
    newRoute.locations = [];
    newRoute.ids = [];
    while(route.locations.getLength()>100*i){
        i++;
    }
    var index =0;
    for(var j=0; j<i; j++){
        if(j==i-1){
            subIdList.length = segment.length = route.locations.getLength()-100*i+100;
        }
        var k = 0;
        while(index<(j+1)*100&&index<route.locations.getLength()){
            segment[k]=route.locations.getAt(index);
            subIdList[k]=route.ids[index];
            k++;
            index++;
        }
        newRoute.locations[j]=new google.maps.MVCArray(segment.slice());
        newRoute.ids[j] = subIdList.slice();
    }
    return newRoute;
}


//send snap-to-road request and handle callback
function requestSnap(route){
    var path = route.locations;
    var id_list = route.ids;
    var pathValues = [];
    for (var i = 0; i < path.getLength(); i++) {
        pathValues.push(path.getAt(i).toUrlValue());
    }
    $.get('https://roads.googleapis.com/v1/snapToRoads', {
        interpolate: false,
        key: apiKey,
        path: pathValues.join('|')
    }, function(data) {
        //store response in local
        processSnapToRoadResponse(data,id_list);
    });
}

// Store snapped polyline returned by the snap-to-road method.
function processSnapToRoadResponse(data,id_list) {
    var result = [];
    var length = data.snappedPoints.length<id_list.length ? data.snappedPoints.length : id_list.length;
    for (var i = 0; i < length; i++) {
        var lat = data.snappedPoints[i].location.latitude;
        var lng = data.snappedPoints[i].location.longitude;
        /*var latlng = new google.maps.LatLng(
            data.snappedPoints[i].location.latitude,
            data.snappedPoints[i].location.longitude);
        snappedCoordinates.push(latlng);*/
        var snaped = {lat:lat,lng:lng,location_id:id_list[i]};
        result.push(snaped);
    }
    postResults(result);

}

function postResults(result){
    $.post("snapRoute.php",{
        data: result},
        function(data,status){
            alert("Data: " + data + "\nStatus: " + status);
        });
    //alert(counter + "Data: " + snappedCoordinates.toString() + "length" + snappedCoordinates.length);
}

