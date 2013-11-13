<?php
$db = new SQLite3('database.db');
$table = 'files';

$perPage = 9;
$columns = sqrt($perPage);
$thumbWidth = 175;
$thumbHeight = 175;
?>
