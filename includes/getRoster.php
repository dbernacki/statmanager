<?php
$id = filter_input(INPUT_GET, "id");
include "dbfunctions.php";
$players = getPlayers($id);

echo '
<form class="ui-filterable">
	<input id="playerFilter" data-type="search" placeholder="Search for player..">
</form>
<ul data-role="listview" data-inset="true" data-filter="true" data-input="#playerFilter" class="ui-responsive">
	<li data-role="list-divider" class="ui-grid-b">
		<div class="ui-block-a">Player Name</div>
		<div class="ui-block-b">Jersey</div>
		<div class="ui-block-c">Status</div>
	</li>';
if (!empty($players)) {
	for ($p = 0; $p < count($players); $p++) {
		echo '<li class="ui-grid-b player" id="' . $players[$p]['id'] . '" team="'.$id.'">
				<div class="ui-block-a playerName">'.$players[$p]['Name'].'</div>
				<div class="ui-block-b playerJersey">'.$players[$p]['Jersey'].'</div>';
		if ($players[$p]['active']) {
			echo '<div class="ui-block-c playerActive" playerActive="'.$players[$p]['active'].'">Active</div>';
		} else {
			echo '<div class="ui-block-c playerActive error" playerActive="'.$players[$p]['active'].'">Inactive</div>';
		}
		echo '</li>';
	}
}
echo "</ul>";