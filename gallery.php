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


// Add new images to database
$imageFiles = glob($imageDir . '*.{png,gif,jpg,jpeg,webp}', GLOB_BRACE);

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

  if (!$isInDB) {
    $db -> exec('INSERT INTO "'.$table.'" (name) VALUES ("'.$currFile.'")');
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

  echo "    <form>\n";
  echo "      <input type='text' name='tag' placeholder='Enter tag to add' autofocus>\n";
  echo "    </form>\n";
  echo "    <form>\n";
  echo "      <input type='text' name='delTag' placeholder='Enter tag to delete'>\n";
  echo "    </form>\n";
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
