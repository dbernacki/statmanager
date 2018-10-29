<?php 
include "dbfunctions.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$addFixtureSeason = filter_input(INPUT_POST,"addFixtureSeason");
    $addFixtureDate = filter_input(INPUT_POST, "addFixtureDate");
    $addFixtureType = filter_input(INPUT_POST, "addFixtureType");
    $addFixtureDivision = filter_input(INPUT_POST, "addFixtureDivision");
    $addFixtureHomeTeam = filter_input(INPUT_POST, "addFixtureHomeTeam");
    $addFixtureAwayTeam = filter_input(INPUT_POST, "addFixtureAwayTeam");
    $addFixtureHour = filter_input(INPUT_POST, "addFixtureHour");
    $addFixtureField = filter_input(INPUT_POST, "addFixtureField");

    //echo $addFixtureDate." ".$addFixtureHour.":00\n".$addFixtureDivision."\n".$addFixtureHomeTeam." vs ".$addFixtureAwayTeam."\n".$addFixtureField;
    if (insertFixture($addFixtureSeason,$addFixtureDate,$addFixtureType,$addFixtureDivision,$addFixtureHomeTeam,$addFixtureAwayTeam,$addFixtureHour,$addFixtureField)) {
        echo "\n\nFIXTURE ADDED SUCCESSFULLY";
    } else {
        echo "\n\nFIXTURE WAS NOT ADDED";
    }
}
?>