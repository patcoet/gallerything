<?php
if (!isset($dbFile)) {
  include 'constants.php';
}

$page = (isset($_GET['p']) ? $_GET['p'] - 1 : 0);

// TODO: Simplify this and make it actually work right
/*if ($page + 1 == ceil(count($images)/$perPage)) {
  $max = $perPage - count($images) % $perPage;
} else {
  $max = $perPage;
}*/
//if ($max == 0) {
  $max = $perPage;
//}

for ($i = 0; $i < $max; $i++) {
  $fileName = $images[$i+$perPage*$page];

  include 'thumbGen.php';

  if ($fileName) {
    $filePath = $imageDir . $fileName;
    $fileName = urlencode($fileName);
    $currThumb = urlencode($currThumb);
    echo "          <div class='img'>\n";
    echo "            <a href='viewer.php?i=$fileName'>\n"; // TODO: Should this send database row number instead of filename?
    echo "              <img src='$currThumb' alt='$filePath'>\n";
    echo "            </a>\n";
    echo "          </div>\n";
  }
}
?>
