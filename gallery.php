<?php
include 'constants.php';

if (!file_exists($imageDir)) {
  mkdir($imageDir);
}

if (!file_exists($thumbsDir)) {
  mkdir($thumbsDir);
}

// Get variables
$searching = (isset($_GET['searching']) ? $_GET['searching'] : null);

$imageFiles = glob($imageDir . '*.{png,gif,jpg,jpeg,webp}', GLOB_BRACE);
// sort($imageFiles);

// $files = glob("uploaded_files/*.*");
usort($imageFiles, function ($a, $b) {
   return filemtime($a) - filemtime($b);
});


for ($i = 0; $i < count($imageFiles); $i++) {
  $currFile = substr($imageFiles[$i], 4);

  if ($imageFiles[$i] != str_replace(' ', '_', $imageFiles[$i])) {
    rename($imageFiles[$i], str_replace(' ', '_', $imageFiles[$i]));
  }

  $isInDB = $db -> prepare('SELECT name FROM "'.$table.'" WHERE name LIKE "'.$currFile.'"');
  $isInDB = $isInDB -> execute();
  $isInDB = $isInDB -> fetchArray();
  $isInDB = $isInDB[0];

/*
  $size = filesize($imageFiles[$i]);      // Slowdowns, no real utility
  $img = new Imagick($imageFiles[$i]);    // Slowdown is only significant when adding
  $width = $img -> getImageWidth();       // many files, though, so might as well
  $height = $img -> getImageHeight();     // TODO: Look at Imagick::Get* functions
*/

  if (!$isInDB) {
    $db -> exec('INSERT INTO "'.$table.'" (name) VALUES ("'.$currFile.'")');
    echo "adding $currFile...<br>\n";
  }
}

// Display search/sort/tag results
include 'filter.php';

// HTML page header
include 'header.php';

include 'adminPanel.php';

// Show the menu section
echo "    <div class='outerContainer'>\n";
echo "      <div class='menu'>\n";
echo "        ";
include 'separator.php';
echo "        <span class='title'>\n";
echo "          <a href='gallery.php'>A Gallery</a><br>\n";
echo "        </span>\n";
echo "        <span class='buttons'>\n";
echo "          <a href='?searching=1'>Search</a><br>\n";
echo "        </span>\n";
echo "        ";
include 'separator.php';

// Display search box/sort options/tags
if ($searching || $search) {
  echo "        <form><input type='search' name='s' placeholder='Search (filename)' autofocus></form>\n";
} else {
  $stmt = $db -> prepare('SELECT * FROM "'.$table.'"');
  $result = $stmt -> execute();
  $tagList = array();
  $l = $result -> numColumns();
  for ($k = 1; $k < $l; $k++) {
    $tagList[$k-1] = $result -> columnName($k);
  }
  sort($tagList);
  for ($i = 0; $i < count($tagList); $i++) {
    $currTag = $tagList[$i];
    $tagName = $currTag;
    $currTag = urlencode($currTag);
    $tagLink = "      <a href='?t=$currTag'>$tagName</a>";
    $tags = $orgTag . ',' . $currTag;
    if ($tag) {
      $tagLink = "<span class='deadLink'>(+) " . $tagLink . " <a href='?t=$tags'>(+)</a>";
    }
    echo $tagLink . "<br>\n";
  }
}

// End of menu div
echo "      </div>\n\n";
echo "      <div class='innerContainer'>\n";

// Display thumbnails
echo "        <div class='thumbnails'>\n";

if (file_exists($imageDir) && file_exists($thumbsDir)) {
  include 'thumbs.php';
}

echo "        </div>\n";
echo "        <div class='pager'><br>\n  ";
echo "        ";
include 'separator.php';
echo "          ";

// Display page switcher
include 'pageSwitcher.php';

echo "        </div>\n";
echo "      </div>\n\n";
echo "    </div>\n";
echo "    $feedback"; // Show whatever errors or confirmation messages we have
echo "  </body>\n";
echo "</html>";
?>
