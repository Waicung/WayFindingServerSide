<?php

/**
 * Created by PhpStorm.
 * User: waicung
 * Date: 24/04/2016
 * Time: 23:22
 */

class stepsCreater
{
    function createStep($conn,$route_id, $steps){
        echo "route: ", $route_id;

        $step_number = 1;
        $lastStep = end($steps);
        foreach($steps as $step){
            $step_instruction = $step['instruction'];
            echo "step: ", $step_number, "ins: ", $step_instruction;
            //insert step row
            if ($step == $lastStep){
                $last_insert_query = "INSERT INTO steps (route_id,step_number,instruction) VALUE ($route_id,$step_number,'$step_instruction')";
                mysqli_query($conn,$last_insert_query);
            }else {
                $step_distance = $step['distance'];
                echo "dis: ", $step_distance;
                $step_duration = $step['duration'];
                echo "dur: ", $step_duration;
                $step_start_location = $step['start_point'];
                $step_end_location = $step['end_point'];
                $step_start_lat = $step_start_location['lat'];
                $step_start_lng = $step_start_location['lng'];
                $step_end_lat = $step_end_location['lat'];
                $step_end_lng = $step_end_location['lng'];
                //update way_points get point_id
                $pointUpdator = new pointsUpdater();
                $step_start_id = $pointUpdator->updatePoint($conn,$step_start_lat,$step_start_lng);
                $step_end_id = $pointUpdator->updatePoint($conn,$step_end_lat,$step_end_lng);
                echo "points: " , $step_start_id," ", $step_end_id;
                $insert_query = "INSERT INTO steps (route_id,step_number,start_location,end_location,distance,duration,instruction) VALUES ($route_id,$step_number,$step_start_id,$step_end_id,$step_distance,$step_duration,'$step_instruction')";
                mysqli_query($conn, $insert_query);
            }
            $step_number ++;

        }
    }

}