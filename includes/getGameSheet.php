<?php
include 'dbfunctions.php';
$gameId = filter_input(INPUT_GET, 'gameId');
$gameSheet = getGame($gameId);
echo json_encode($gameSheet);