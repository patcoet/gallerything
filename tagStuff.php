$tag = (isset($_GET['tag']) ? $_GET['tag'] : null);
$delTag = (isset($_GET['delTag']) ? $_GET['delTag'] : null);


// Add tag
if ($tag) {
  $db -> exec('ALTER TABLE "'.$table.'" ADD "'.$tag.'" string');
}

// Delete tag
if ($delTag) {
  $delTag = strtolower($delTag);
  $col = $db -> prepare('SELECT * FROM "'.$table.'"');
  $col = $col -> execute();
  $numCols = $col -> numColumns();
  $db -> exec('CREATE TABLE temptable(name string)');
  $columnsToCopy = 'name';
  for ($i = 1; $i < $numCols; $i++) {
    $currColumnName = $col -> columnName($i);
    if ($currColumnName != $delTag) {
      $db -> exec('ALTER TABLE temptable ADD "'.$currColumnName.'" string');
      $columnsToCopy = $columnsToCopy . ', "' . $currColumnName . '"';
    }
  }
  $argument = "INSERT INTO temptable SELECT $columnsToCopy FROM $table";
  $db -> exec($argument);

  $db -> exec('ALTER TABLE "'.$table.'" RENAME TO temp2;
               ALTER TABLE temptable RENAME TO "'.$table.'";
               DROP TABLE temp2');
}

echo "    <form>\n";
echo "      <input type='text' name='tag' placeholder='Enter tag to add'>\n";
echo "    </form>\n";
echo "    <form>\n";
echo "      <input type='text' name='delTag' placeholder='Enter tag to delete'>\n";
echo "    </form>\n";