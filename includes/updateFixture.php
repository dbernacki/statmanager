<?php 
include "dbfunctions.php";

$updateFixtureId = filter_input(INPUT_POST, "updateFixtureId");
$updateFixtureSeason = filter_input(INPUT_POST, "updateFixtureSeason");
$updateFixtureDate = filter_input(INPUT_POST, "updateFixtureDate");
$updateFixtureType = filter_input(INPUT_POST, "updateFixtureType");
$updateFixtureDivision = filter_input(INPUT_POST, "updateFixtureDivision");
$updateFixtureHome = filter_input(INPUT_POST, "updateFixtureHomeTeam");
$updateFixtureAway = filter_input(INPUT_POST, "updateFixtureAwayTeam");
$updateFixtureHour = filter_input(INPUT_POST, "updateFixtureHour");
$updateFixtureField = filter_input(INPUT_POST, "updateFixtureField");

if (updateFixture($updateFixtureId, $updateFixtureSeason, $updateFixtureDate, $updateFixtureType, $updateFixtureDivision, $updateFixtureHome, $updateFixtureAway, $updateFixtureHour, $updateFixtureField)) {
    echo "\n\nFixture Edit Successful";
} else {
    echo "\n\nFixture Edit Unsuccessful";
}