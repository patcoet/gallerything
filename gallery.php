<?php // TODO: Security tester
// DRY
include 'constants.php';

// Initial setup
include 'setup.php';

// Get variables
$searching = (isset($_GET['searching']) ? $_GET['searching'] : null);

// Are we deleting a file?
include 'delete.php';

// Display search/sort/tag results
include 'filter.php';

// HTML page header
include 'header.php';

// Show the admin panel
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
echo "          <a href='?searching=1'>Search</a> | <a href='?tagging=1'>Tags</a><br>\n";
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
echo "    $feedback"; // Show whatever errors or confirmation messages we got
echo "  </body>\n";
echo "</html>";
?>
