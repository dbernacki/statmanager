<?php 
include "dbfunctions.php";

$addPlayerName = $addPlayerTeam = $addPlayerJersey = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$addPlayerName = filter_input(INPUT_POST, "addPlayerName");
	$addPlayerTeam = filter_input(INPUT_POST, "teamRosterSelect");
    $addPlayerJersey = filter_input(INPUT_POST, "addPlayerJersey");
	
    echo "Name: ".$addPlayerName."\nTeam ID: ".$addPlayerTeam."\nJersey: ".$addPlayerJersey."\nActive: ".$addPlayerActive;
    if (insertPlayer($addPlayerName, $addPlayerTeam, $addPlayerJersey)) {
        echo "\n\nPLAYER ADDED SUCCESSFULLY";
    } else {
        echo "\n\nPLAYER WAS NOT ADDED";
    }
}
?>