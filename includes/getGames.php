<?php 
include "dbfunctions.php";
$date = filter_input(INPUT_GET, 'date');
$divisions = getDivisions();
$games = getDateFixtures($date);
echo '<option disabled="disabled" selected="selected">Select gamesheet...</option>';
for ($d = 0; $d < count($divisions); $d++) {
    echo '<optgroup label="' . $divisions[$d]['Division'] . ' Division">';
    for ($f = 0; $f < count($games); $f++) {
        if ($games[$f]['division'] == $divisions[$d]['division_id']) {
            echo '<option value="' . $games[$f]['id'] . '"';
            if ($games[$f]['complete']) {
                echo ' class="complete"';
            }
            echo '>' . $games[$f]['Home'] . ' vs ' . $games[$f]['Away'] . '</option>';
        }
    }
}