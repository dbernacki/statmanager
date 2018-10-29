<?php
include "dbfunctions.php";

$gameId = filter_input(INPUT_POST, "gameId");
$homeScore = filter_input(INPUT_POST, "homeScore");
$awayScore = filter_input(INPUT_POST, "awayScore");

if (updateGameFixture($gameId, $homeScore, $awayScore)) {
    echo "Fixture score updated! Home: " . $homeScore . " - " . $awayScore . ":Away";
} else {
    echo "Fixture score unchanged";
}