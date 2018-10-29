<?php
include 'dbfunctions.php';
$season = filter_input(INPUT_GET, 'season');
$fixtures = getFixtures($season);
$fixtureDates = getFixtureDates($season);

echo '<ul data-role="listview" data-inset="true" class="ui-responsive" id="fixtureList">';
for ($d = 0; $d < count($fixtureDates); $d++) {
	echo '<li data-role="list-divider">Week ' . ($d + 1) . ': ' . date('Y-m-d', strtotime($fixtureDates[$d]["date"])) . '</li>';
	for ($f = 0; $f < count($fixtures); $f++) {
		if ($fixtures[$f]['date'] == $fixtureDates[$d]['date']) {
			echo '
			<li class="ui-grid-b fixture" id="' . $fixtures[$f]['id'] . '" complete="' . $fixtures[$f]['complete'] . '">
				<div class="ui-block-a">' . $fixtures[$f]['Home'] . ' vs '.$fixtures[$f]['Away'] . '</div>
				<div class="ui-block-b">';
			if ($fixtures[$f]['Time'] < 10) {
				echo '0';
			}
			echo $fixtures[$f]['Time']. ':00</div>
				<div class="ui-block-c">' . $fixtures[$f]['Field'] . '</div>
			</li>';
		}
	}
}
echo '</ul>';
