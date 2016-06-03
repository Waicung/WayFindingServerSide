<?php
/**
 * Created by PhpStorm.
 * User: waicung
 * Date: 02/06/2016
 * Time: 18:13
 */


function initialSpeedComputation($locations){
    $result = array();
    $pointObject = array();
    $window = 5;
    $interval = 10;
    $counter = 0;
    for($i=9; $i<count($locations)-$window;$i+=$interval){
        $location_id = $locations[$i]['location_id'];
        $lat1=doubleval($locations[$i-$window]['lat']);
        $lng1=doubleval($locations[$i-$window]['lng']);
        $time1=$locations[$i-$window]['time_stamp'];
        $lat2=doubleval($locations[$i+$window]['lat']);
        $lng2=doubleval($locations[$i+$window]['lng']);
        $time2=$locations[$i+$window]['time_stamp'];
        $speed = computeSpeed($lat1,$lng1,$time1,$lat2,$lng2,$time2);
        if($speed>=0){
            $pointObject['location_id'] = $location_id;
            $pointObject['speed'] = $speed;
            array_push($result,$pointObject);
        }else{
            //echo "error";
        }
        //echo ++$counter;
    }
    return $result;

}

function computeSpeed($lat1, $lon1, $time1, $lat2, $lon2, $time2){
    $distance = distance($lat1, $lon1, $lat2, $lon2, "K");
    $datetime1 = new DateTime($time1);
    $datetime2 = new DateTime($time2);
    $interval = $datetime1->diff($datetime2);
    $speed = $distance/intval($interval->format('%s'));
    return $speed*1000;
}

function distance($lat1, $lon1, $lat2, $lon2, $unit) {

    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
        return ($miles * 1.609344);
    } else if ($unit == "N") {
        return ($miles * 0.8684);
    } else {
        return $miles;
    }
}