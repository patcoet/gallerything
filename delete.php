<?php
$fileToDelete = '';
if (isset($_GET['del'])) {
  $fileToDelete = $_GET['del'];
}

if ($fileToDelete != '') {
  unlink($imageDir . $fileToDelete); // TODO: use $imgdir instead and have the value of that set in a central place (variables.php or whatever)
  unlink($thumbsDir . $fileToDelete);
  $db -> exec('DELETE FROM "'.$table.'" WHERE name="'.$fileToDelete.'"');
}
?>
