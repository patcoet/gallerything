<?php
$fileToDelete = (isset($_GET['del']) ? $_GET['del'] : null);

if ($fileToDelete != '') {
  unlink($imageDir . $fileToDelete);
  unlink($thumbsDir . $fileToDelete);
  $db -> exec('DELETE FROM "'.$table.'" WHERE name="'.$fileToDelete.'"');

  $col = $db -> prepare('SELECT * FROM "'.$table.'"');
  $col = $col -> execute();
  $numCols = $col -> numColumns();
  $db -> exec('CREATE TABLE temptable (name string)');
  for ($i = 1; $i < $numCols; $i++) {
    $currColumnName = $col -> columnName($i);
    $db -> exec('ALTER TABLE temptable ADD "'.$currColumnName.'" string');
  }

  $db -> exec('INSERT INTO temptable SELECT * from "'.$table.'"');

  $db -> exec('ALTER TABLE "'.$table.'" RENAME TO temp2;
               ALTER TABLE temptable RENAME TO "'.$table.'";
               DROP TABLE temp2');

}
?>
