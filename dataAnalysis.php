<?php
/**
 * Created by PhpStorm.
 * User: waicung
 * Date: 03/06/2016
 * Time: 11:25
 */

require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/myfunctions.php';
$conn=mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
$response = array();
$search_query = "SELECT l.location_id,l.record_id,latitude,longitude,time_stamp,a.route_id,
FROM locations AS l
LEFT JOIN recordings AS r
ON l.record_id = r.record_id
LEFT JOIN route_assignments AS a
ON r.assignment_id=a.assignment_id
LEFT JOIN steps AS s
ON s.step_number = l.step AND a.route_id = s.route_id
WHERE l.record_id>15
AND latitude<>0
ORDER BY route_id,record_id, time_stamp";
$result = mysqli_query($conn,$search_query);