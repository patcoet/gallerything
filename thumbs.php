<?php
if (!isset($dbFile)) {
  include 'constants.php';
}

$page = '';
if (isset($_GET['p'])) {
  $page = $_GET['p'];
}

for ($i = 0; $i < (count($images) % $perPage); $i++) {
  $fileName = $images[$i+$perPage*$page];

  include 'thumbGen.php';

  if ($fileName) {
    $filePath = $imageDir . $fileName;
    $fileName = urlencode($fileName);
    $currThumb = urlencode($currThumb);
    echo "          <div class='img'>\n";
    echo "            <a href='viewer.php?i=$fileName'>\n"; // Should this send database row number instead of filename?
    echo "              <img src='$currThumb' alt='$filePath'>\n";
    echo "            </a>\n";
    echo "          </div>\n";
  }
}
?>
