<?php
$dbFile = 'database.db';
$db = new SQLite3($dbFile);
$table = 'files';

$imageDir = 'img/';
$thumbsDir = 'thumbs/';

$perPage = 9;
$columns = sqrt($perPage);
$thumbWidth = 175;
$thumbHeight = 175;

$home = $_SERVER['PHP_SELF'];
$home = pathinfo($home);
$home = $home['basename'];

$feedback = '';

$firstTime = 0;
?>
