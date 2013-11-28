<?php
$fileToDelete = (isset($_GET['del']) ? $_GET['del'] : null);

if ($fileToDelete != '') {
  unlink($imageDir . $fileToDelete);
  unlink($thumbsDir . $fileToDelete);
  $db -> exec('DELETE FROM "'.$table.'" WHERE name="'.$fileToDelete.'"');
}
?>
