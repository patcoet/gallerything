<?php
// DRY
include 'constants.php';

// Get variables
$searching = $_GET['searching'];
$logout = $_GET['logout'];
$pass = $db -> prepare('SELECT password FROM users');
$pass = $pass -> execute();
$pass = $pass -> fetchArray();
$pass = $pass[0];

// Are we deleting a file?
include 'delete.php';

// Display search/sort/tag results
include 'filter.php';

// Make sure the thumbnails directory exists (unnecessary and/or should be in admin.php?)
include 'setup.php';

// HTML page header
include 'header.php';

// Show the admin panel
include 'adminPanel.php';

// Show the menu section
echo "    <div class='outerContainer'>\n";
echo "      <div class='menu'>\n";
include 'separator.php';
echo "        <span class='title'>\n";
echo "          <a href='gallery.php'>A Gallery</a><br>\n";
echo "        </span>\n";
echo "        <span class='buttons'>\n";
echo "          <a href='?searching=1'>Search</a> | <a href='?tagging=1'>Tags</a><br>\n";
echo "        </span>\n";
include 'separator.php';

// Display search box/sort options/tags
if ($searching || $search) {
  echo "        <form><input type='text' name='s' placeholder='Search (filename)' size=12 autofocus></form>\n";
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
    echo "        <a href='?t=$currTag'>$tagName</a>";
    $tags = $orgTag . ',' . $currTag;
    if ($tag) {
      echo " <a href='?t=$tags'>(+)</a><br>\n";
    } else {
      echo "<br>\n";
    }
  }
}

// End of menu div
echo "      </div>\n\n";
echo "      <div class='innerContainer'>\n";

// Display thumbnails
$w = $thumbWidth * $columns;
echo "        <div class='thumbnails'>\n";

if (file_exists('img') && file_exists('thumbs')) {
include 'thumbs.php';
}

echo "        </div>\n";
echo "        <div class='pager'><br>\n  ";
include 'separator.php';

// Display page switcher
include 'pageSwitcher.php';

echo "        </div>\n";
echo "      </div>\n\n";
echo "    </div>\n";
echo "  </body>\n";
echo "</html>";
?>
