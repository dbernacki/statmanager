<?php
$id = filter_input(INPUT_GET, "id");
include "dbfunctions.php";
$player = getPlayer($id);
echo json_encode($player);