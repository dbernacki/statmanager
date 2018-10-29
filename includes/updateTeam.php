<?php 
include "dbfunctions.php";

$id = filter_input(INPUT_POST, "id");
$name = filter_input(INPUT_POST, "updateTeamName");
$division = filter_input(INPUT_POST, "updateTeamDivision");
$group = filter_input(INPUT_POST, "updateTeamGroup");
$manager = filter_input(INPUT_POST, "updateTeamManager");
$email = filter_input(INPUT_POST, "updateTeamEmail");
$phone = filter_input(INPUT_POST, "updateTeamPhone");
if (filter_input(INPUT_POST, "updateTeamActive")) {
    $active = true;
} else {
    $active = false;
}
	
echo "Name: ".$name."\nDivision: ".$division."\nGroup: ".$group."\nManager: ".$manager."\nEmail: ".$email."\nPhone: ".$phone."\nActive: ".$active;
if (updateTeam($name,$division,$group,$manager,$email,$phone,$active,$id)) {
    echo "\n\nTeam edit successful";
} else {
    echo "\n\nTeam edit unsuccessful";
}
?>