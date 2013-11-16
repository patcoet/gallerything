<?php
$db = new SQLite3('database.db');
$table = 'files';

$imageDir = 'img/';
$perPage = 9;
$columns = sqrt($perPage);
$thumbWidth = 175;
$thumbHeight = 175;

$home = $_SERVER['PHP_SELF'];
$home = pathinfo($home);
$home = $home['basename'];
?>
