<?php
$teamId = filter_input(INPUT_GET, "teamId");
include "dbfunctions.php";
$roster = getGameSheetRoster($teamId);

echo '<ul data-role="listview" data-inset="true">'
        . '<li data-role="list-divider" class="ui-grid-d">'
            . '<div class="ui-block-a">Name</div>'
            . '<div class="ui-block-b">G</div>'
            . '<div class="ui-block-c">Y</div>'
            . '<div class="ui-block-d">R</div>'
            . '<div class="ui-block-e">P</div>'
        . '</li>';
for ($h = 0; $h < count($roster); $h++) {
    echo '<li id="'.$roster[$h]['id'].'" class="ui-grid-d gameSheetPlayer" update="false">'
            . '<div class="ui-block-a">'.$roster[$h]['jersey'].'<br>' . $roster[$h]['name'] . '</div>'
            . '<div class="ui-block-b"><input type="number" class="goals" data-inline="true" data-mini="true" min="0" max="10" value="0"></input></div>'
            . '<div class="ui-block-c"><input type="number" class="yellow" data-mini="true" min="0" max="2"value="0"></input></div>'
            . '<div class="ui-block-d"><input type="number" class="red" data-mini="true" min="0" max="1" value="0"></input></div>'
            . '<div class="ui-block-e"><input type="checkbox" class="played" data-mini="true"></input></div>'
        . '</li>';
}
echo '</ul>';