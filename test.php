<?php
echo "<p>";

$db = new SQLite3('mysqlitedb.db');

//$db -> exec('CREATE TABLE foo (id INTEGER, bar STRING)');
//$db -> exec('INSERT INTO foo (id, bar) VALUES (1, "This is a test")');
//$db -> exec('INSERT INTO foo (id, bar) VALUES (2, "whoa")');

for ($i = 1; $i < 3; $i++) {
$stmt = $db -> prepare('SELECT * FROM foo WHERE id=:id');
$stmt -> bindValue(':id', $i);

$result = $stmt -> execute();

$butt = $result -> fetchArray();

echo $butt['id'];
}

echo "</p>butt";
?>





