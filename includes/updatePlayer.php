<?php 
include "dbfunctions.php";

$updatePlayerId = filter_input(INPUT_POST, "id");
$updatePlayerName = filter_input(INPUT_POST, "updatePlayerName");
$updatePlayerTeam = filter_input(INPUT_POST, "updatePlayerTeam");
$updatePlayerJersey = filter_input(INPUT_POST, "updatePlayerJersey");

if (isset($_POST['updatePlayerActive'])) {
    $updatePlayerActive = true;
}

echo "Name: ".$updatePlayerName."\nTeam: ".$updatePlayerTeam."\nJersey: ".$updatePlayerJersey."\nActive: ".$updatePlayerActive;
if (updatePlayer($updatePlayerId,$updatePlayerName,$updatePlayerTeam,$updatePlayerJersey,$updatePlayerActive)) {
    echo "\n\nPlayer Edit Successful";
} else {
    echo "\n\nPlayer Edit Unsuccessful";
}

?>