<?php
include "dbfunctions.php";

$divisions = getDivisions();

echo '<option disabled="disabled" selected>Select team...</option>';
for ($d = 0; $d < count($divisions); $d++) {
	echo '<optgroup label="' . $divisions[$d]['Division'] . ' Division">';
	$teams = getTeams($divisions[$d]['division_id']);
	for ($t = 0; $t < count($teams); $t++) {
		echo '<option value="' . $teams[$t]['team_id'] . '">' . $teams[$t]['Team'] . '</option>';
	}
	echo '</optgroup>';
}