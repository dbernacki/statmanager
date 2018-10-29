<?php 
include "dbfunctions.php";

$addTeamName = $addTeamDivision = "";
$addTeamManager = $addTeamEmail = $addTeamPhone = "-";
$addTeamActive = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$addTeamName = filter_input(INPUT_POST, "addTeamName");
	$addTeamDivision = filter_input(INPUT_POST, "addTeamDivision");
	$addTeamManager = filter_input(INPUT_POST, "addTeamManager");
	$addTeamEmail = filter_input(INPUT_POST, "addTeamEmail");
	$addTeamPhone = filter_input(INPUT_POST, "addTeamPhone");
    if (filter_input(INPUT_POST, "addTeamActive")) {
        $active = true;
    }
	
    echo "Name: ".$addTeamName."\nDivision: ".$addTeamDivision."\nManager: ".$addTeamManager."\nEmail: ".$addTeamEmail."\nPhone: ".$addTeamPhone."\nActive: ".$addTeamActive;
    if (insertTeam($addTeamName,$addTeamDivision,$addTeamManager,$addTeamEmail,$addTeamPhone,$addTeamActive)) {
        echo "\n\nTEAM ADDED SUCCESSFULLY";
    } else {
        echo "\n\nTEAM WAS NOT ADDED";
    }
}
?>