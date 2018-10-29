<?php 
include 'dbfunctions.php';
$divisions = getDivisions();

echo '<ul data-role="listview" data-inset="true" id="teamsTable">';
for ($d = 0; $d < count($divisions); $d++){
	echo '<li data-role="list-divider"><h3>'.$divisions[$d]['Division'].' Division</h3></li>';
	$teams = getTeams_All($divisions[$d]['division_id']);
	for ($t = 0; $t < count($teams); $t++){
		echo
			'<li class="ui-grid-c ui-responsive team" id="'.$teams[$t]['team_id'].'">
				<div class="ui-block-a">'.$teams[$t]['Team'].'</div>
				<div class="ui-block-b">'.$teams[$t]['Manager'].'</div>';
		echo '<div class="ui-block-c">';
		if ($teams[$t]['Group'] == 1) {
			echo 'A';
		} else if ($teams[$t]['Group'] == 2){
			echo 'B';
		}
		echo '</div>';
		if ($teams[$t]['active']) {
			echo '<div class="ui-block-d">Active</div>';
		} else {
			echo '<div class="ui-block-d error">Inactive</div>';
		}
		echo '</li>';
	}
}
echo '</ul>';
?>