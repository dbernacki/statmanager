<?php
include "dbfunctions.php";

$insertUpdateArray = [];
$deleteArray = [];
$intInsertUpdateArray = array();
$intDeleteArray = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $insertUpdateArray = json_decode(filter_input(INPUT_POST, "insertUpdateArray"));
    $deleteArray = json_decode(filter_input(INPUT_POST, "deleteArray"));
    
    //make new arrays with int values instead of strings
    for ($h = 0; $h < count($insertUpdateArray); $h++) {
        $row = array();
        for ($i = 0; $i < count($insertUpdateArray[$h]); $i++) {
            array_push($row, intval($insertUpdateArray[$h][$i]));
        }
        array_push($intInsertUpdateArray, $row);
    }
    for ($h = 0; $h < count($deleteArray); $h++) {
        $row = array();
        for ($i = 0; $i < count($deleteArray[$h]); $i++) {
            array_push($row, intval($deleteArray[$h][$i]));
        }
        array_push($intDeleteArray, $row);
    }

    if (count($intInsertUpdateArray) > 0) {
        if (insertUpdateGame($intInsertUpdateArray)) {
            echo "\nGame sheet successfully updated";
        } else {
            echo "\nGame sheet could not be updated";
        }
    }
    if (count($intDeleteArray) > 0) {
        if (deleteGame($intDeleteArray)) {
            echo "\nPlayers successfully removed from game sheet (in any)";
        } else {
            echo "\nPlayers could not be removed from game sheet (if any)";
        }
    }
}