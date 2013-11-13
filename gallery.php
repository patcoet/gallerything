<?php
// Preferences TODO: permanence (cookies?)
include 'preferences.php';

// DRY
include 'constants.php';

// Get variables
$searching = $_GET['searching'];
$sorting = $_GET['sorting'];

// Are we deleting a file?
include 'delete.php';

// Display search/sort/tag results
include 'filter.php';

// Make sure the thumbnails directory exists (unnecessary and/or should be in admin.php?)
include 'setup.php';

// HTML page header
echo "<html lang='en'>\n";
include 'header.php';
echo "  <body>\n";

// Show the menu section
echo "    <div class='menu'>\n";
echo "      <img src='separator.png'><br>\n";
echo "      <h1>\n";
echo "        <a href='gallery.php'>A Gallery</a><br>\n";
echo "        <h2>\n";
echo "          <a href='?searching=1'>Search</a> | <a href='?sorting=1'>Sort</a> | <a href='?tagging=1'>Tags</a>\n";
echo "        </h2>\n";
echo "      </h1><br>\n";
echo "      <img src='separator.png'><br>\n";

// Display search box/sort options/tags
if ($searching || $search) {
  echo "      <form><input type='text' name='s' placeholder='Search (filename)' size=12 autofocus></form>\n";
} else if ($sorting || $sortMethod) {
  echo "      <h2>\n";
  echo "      <a href='?sort=dateAsc'>Date ▲</a><br>\n";
  echo "      <a href='gallery.php'>Date ▼</a>\n";
  echo "      </h2>\n";
} else if (!$searching && !$sorting) {
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
    echo "      <a href='?t=$currTag'>$currTag</a>";
    $tags = $orgTag . ',' . $currTag;
    if ($tag) {
      echo " <a href='?t=$tags'>(+)</a><br>\n";
    } else {
      echo "<br>\n";
    }
  }
}

// End of menu div
echo "    </div>\n\n";

// Display thumbnails
$w = $thumbWidth * $columns;
echo "    <div style='width:$w'>\n";

include 'thumbs.php';

echo "\n      <img src='separator.png'>\n";
echo "      <h2><br>\n";

// Display page switcher
include 'pageSwitcher.php';

echo "      </h2>\n";
echo "    </div>\n\n";

if ($_POST['password'] == $pass ||  $_COOKIE['authed'] == 1) {
  echo "    <a href=admin.php>Admin</a>\n";
  include 'upload.php';
}

if (!($_COOKIE['authed'] == 1)) {
  if ($_POST['password'] == $pass) {
    setcookie('authed', '1', time()+3600);
  } else {
    echo "    <form action='' method='post'>\n";
    echo "      <input type='password' name='password'>\n";
    echo "    </form>\n\n";
  }
}
echo "  </body>\n";
echo "</html>";
?>
