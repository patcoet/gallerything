<?php
if (!isset($dbFile)) {
  include 'constants.php';
}

$currThumb = $thumbsDir . $fileName;
if (!file_exists($currThumb)) {
  $img = new Imagick($imageDir . $fileName);
  if ($img -> getImageFormat() == 'GIF') {
    $img = $img -> coalesceImages();
  }
  $img -> cropThumbnailImage($thumbWidth, $thumbHeight);
  $img -> writeImage($currThumb);
}
?>
