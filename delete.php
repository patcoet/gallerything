<?php
$fileToDelete = $_GET['del'];

if ($fileToDelete) {
  unlink('img/' . $fileToDelete); // TODO: use $imgdir instead and have the value of that set in a central place (variables.php or whatever)
  unlink('thumbs/' . $fileToDelete);
  $db -> exec('DELETE FROM "'.$table.'" WHERE name="'.$fileToDelete.'"');
}
?>
