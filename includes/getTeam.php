<?php
$id = filter_input(INPUT_GET, "id");
include "dbfunctions.php";
$team = getTeam($id);
echo json_encode($team);