<?php

/**
 * Created by PhpStorm.
 * User: waicung
 * Date: 24/04/2016
 * Time: 22:34
 */

//testing code for insertion when no point found
//return the point_id in both way
/*echo "abcdefg";
require_once __DIR__ . '/db_config.php';
$conn=mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
$updator = new pointsUpdater();
$lat = -37.799901;
$lng = 144.943267;
$result = $updator->updatePoint($conn,$lat,$lng);
echo "abcdefg";
echo $result;*/

class pointsUpdater
{
    function updatePoint($conn, $lat,$lng){
        $search_query = "SELECT * FROM way_points WHERE latitude = '$lat' AND longitude = '$lng'";
        $search = mysqli_query($conn,$search_query);
        if(!empty($search)&&mysqli_num_rows($search)>0){
            $point = mysqli_fetch_array($search);
            //if exist, return point_id
            $point_id = $point['location_id'];
            return $point_id;
        }
        else {
            $insert_query = "INSERT INTO way_points (latitude, longitude) VALUES ($lat,$lng)";
            mysqli_query($conn, $insert_query);
            return $this->updatePoint($conn, $lat, $lng);
        }

    }

}