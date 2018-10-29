<?php
include "dbfunctions.php";
$id = filter_input(INPUT_GET, 'id');
$fixture = getFixture($id);
echo json_encode($fixture);