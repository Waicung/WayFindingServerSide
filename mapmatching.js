var apiKey = 'AIzaSyAZEyaeSOnH8dcVq646GIyUQbxGKHza_dc';
var polylines = [];
var map = [];
var snappedCoordinates = [];
// path is a MVCArray
function initialize(order,path) {
    /*if(path.getLength()<=100){
        runSnapToRoad(path);
        drawSnappedPolyline();
    }else {
        var route = breakLongPath(path);
        for(var i=0;i<route.length;i++) {
            runSnapToRoad(route[i]);
            drawSnappedPolyline(order);
        }
    }*/
    runSnapToRoad(order,path);
}

// Snap a user-created polyline to roads and draw the snapped path
function runSnapToRoad(order,path) {
    var center = {lat:-37.799604, lng:144.957807};
    addMap(center,order);
    snappedCoordinates = [];
    if(path.getLength()>100){
        var route = breakLongPath(path);
        for(var i=0;i<route.length;i++) {
            requestSnap(order,snappedCoordinates,route[i]);
        }
    }
    else{
        requestSnap(order,snappedCoordinates,path);
    }


}

function requestSnap(order,snappedCoordinates,path){
    var pathValues = [];
    for (var i = 0; i < path.getLength(); i++) {
        pathValues.push(path.getAt(i).toUrlValue());
    }
    $.get('https://roads.googleapis.com/v1/snapToRoads', {
        interpolate: true,
        key: apiKey,
        path: pathValues.join('|')
    }, function(data) {
        processSnapToRoadResponse(order,snappedCoordinates,data);
        drawSnappedPolyline(order,snappedCoordinates);
        //drawSnappedPolyline();
    });
}


// Store snapped polyline returned by the snap-to-road method.
function processSnapToRoadResponse(order,snappedCoordinates,data) {
    for (var i = 0; i < data.snappedPoints.length; i++) {
        var latlng = new google.maps.LatLng(
            data.snappedPoints[i].location.latitude,
            data.snappedPoints[i].location.longitude);
        snappedCoordinates.push(latlng);
    }
}

// Draws the snapped polyline (after processing snap-to-road response).
function drawSnappedPolyline(order,snappedCoordinates) {
    var snappedPolyline = new google.maps.Polyline({
        path: snappedCoordinates,
        strokeColor: 'black',
        strokeWeight: 3
    });
    /*snappedCoordinates.forEach(
        function(v){
        addPoint(order,v);
    })*/

    for(var i=0;i<snappedCoordinates.length;i+=10){
        addPoint(order,snappedCoordinates[i],i);
    }

    snappedPolyline.setMap(map[order]);
    polylines.push(snappedPolyline);
}

function addPoint(order,latlng,i){
    new google.maps.Marker({
        position: latlng,
        map: map[order],
        label: ""+i,
    });
}

function breakLongPath(path){
    var i = 1;
    var segment = [];
    var route = [];
    while(path.getLength()>100*i){
        i++;
    }
    var index =0;
    for(var j=0; j<i; j++){
        if(j==i-1){
            segment.length = path.length-100*i+100;
        }
        var k = 0;
        while(index<(j+1)*100&&index<path.length){
            segment[k]=path.getAt(index);
            k++;
            index++;
        }
        route[j]=new google.maps.MVCArray(segment.slice());
    }
    return route;
}

function addMap(center,order){
    $("#map").remove();
    $("#map-container").prepend("<div class='col-sm-4'><div class='floating-title'>Test "+order+"</div><div class='map' id='map" + order+1 +"'></div></div>");
    map[order] = new google.maps.Map(document.getElementById('map'+order), {
        zoom: 15,
        center: center,
        //mapTypeId: google.maps.MapTypeId.TERRAIN
    });
}




