<?php
include 'constants.php';
$page = $_GET['p'];

for ($i = 0; $i < $perPage; $i++) {
  $currImage = $images[$i+$perPage*$page];

  $currThumb = 'thumbs/' . $currImage;
  if (!file_exists($currThumb)) {
    $img = new Imagick('img/' . $currImage);
    if ($img -> getImageFormat() == 'GIF') {
      $img = $img -> coalesceImages();
    }
    $img -> cropThumbnailImage($thumbWidth, $thumbHeight);
    $img -> writeImage($currThumb);
  }

  if ($currImage) {
    if ($sortMethod) {
      $currImage = $currImage . '&sort=' . $sortMethod;
    }
    echo "      <div class='img'>\n";
    echo "        <a href=viewer.php?i=$currImage>\n"; // Should this send database row number instead of filename?
    echo "          <img src=$currThumb>\n";
    echo "        </a>\n";
    echo "      </div>\n";
  }
}
?>
