<?php
include "dbfunctions.php";

$homeGameArray = [];
$awayGameArray = [];
$insertData = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $homeGameArray = json_decode(filter_input(INPUT_POST, 'homeGameArray'));
    $awayGameArray = json_decode(filter_input(INPUT_POST, 'awayGameArray'));

    for ($h = 0; $h < count($homeGameArray); $h++) {
        $row = array();
        for ($i = 0; $i < count($homeGameArray)[$h]; $i++) {
            array_push($row, intval($homeGameArray[$h][$i]));
        }
        array_push($insertData, $row);
    }
    for ($h = 0; $h < count($awayGameArray); $h++) {
        $row = array();
        for ($i = 0; $i < count($awayGameArray)[$h]; $i++) {
            array_push($row, intval($awayGameArray[$h][$i]));
        }
        array_push($insertData, $row);
    }

    $insertData = array_merge($homeGameArray, $awayGameArray);
    $numPlayers = count($insertData);
    
//    print_r($insertData);
//    foreach ($insertData as $x) {
//        print_r($x);
//    }
    
    if (insertGame($insertData)) {
        echo $numPlayers . " players added to game sheet. Game sheet Completed!";
    } else {
        echo "Game Sheet could not be completed";
    }
}