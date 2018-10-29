<?php 
include "dbfunctions.php";
$season = filter_input(INPUT_GET, "season");
$fixtureDates = getFixtureDates($season);
echo '<option disabled="disabled" selected="selected">Select date...</option>';
for ($d = 0; $d < count($fixtureDates); $d++) {
	echo "<option value='". $fixtureDates[$d]['date'] ."'>Week " . ($d + 1) . ": " . $fixtureDates[$d]['date'] . "</option>";
}