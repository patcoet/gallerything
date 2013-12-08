<?php
// TODO: search prev next
include 'constants.php';

// Get the decoded filename of the image we're displaying
$img = (isset($_GET['i']) ? urldecode($_GET['i']) : null);

// Set or unset a tag
$tag = (isset($_GET['t']) ? $_GET['t'] : null);
if ($tag) {
  if (substr($tag, 0, 1) == "-") {
    $op = 0;
  } else {
    $op = 1;
  }
  $tag = substr($tag, 1);
  $db -> exec('UPDATE "'.$table.'" SET "'.$tag.'"="'.$op.'" WHERE name="'.$img.'"');
}

// Get a list of available tags
$stmt = $db -> prepare('SELECT * FROM "'.$table.'"');
$result = $stmt -> execute();
$tagList = array();
for ($i = 1; $i < $result -> numColumns(); $i++) { // Note: the 1 assumes that column 1 is the first tag column; shouldn't really be hardcoded
  $tagList[$i-1] = $result -> columnName($i);
}
sort($tagList);

// Get name of previous and next image, if they exist
$curr = $db -> prepare('SELECT rowid FROM "'.$table.'" WHERE name="'.$img.'"');
$curr = $curr -> execute();
$curr = $curr -> fetchArray();
$curr = $curr[0];
$rowID = $curr - 1;
$prev = $db -> prepare('SELECT name FROM "'.$table.'" WHERE rowid="'.$rowID.'"');
$prev = $prev -> execute();
$prev = $prev -> fetchArray();
$prev = $prev[0];
$rowID = $curr + 1;
$next = $db -> prepare('SELECT name FROM "'.$table.'" WHERE rowid="'.$rowID.'"');
$next = $next -> execute();
$next = $next -> fetchArray();
$next = $next[0];

// Begin HTML output
include 'header.php';

$authed = (isset($_COOKIE['authed']) ? $_COOKIE['authed'] : 0);
if ($authed == '1') {
  echo "    <div class='admin'>\n";
  echo "      <a href='gallery.php?del=$img'>Delete file</a><br>\n";
  echo "    </div>\n";
}

echo "    <div class='menu'>\n";
include 'separator.php';
echo "      <span class='title'>\n";
echo "        <a href='gallery.php'>A Gallery</a>\n";
echo "      </span><br>\n";
echo "      <span class='buttons'>\n";

if ($prev) {
  echo "        <a href='?i=$prev'>&lt;&lt; Prev</a>";
} else {
  echo "<span class='deadLink'>&lt;&lt; Prev</span>";
}
if ($prev && $next) {
  echo " | ";
} else {
  echo "<span class='deadLink'> | </span>";
}
if ($next) {
  echo "<a href='?i=$next'>Next >></a>\n";
} else {
  echo "<span class='deadLink'>Next >></span>";
}

echo "      </span><br>\n";
include 'separator.php';

for ($i = 0; $i < count($tagList); $i++) {
  $currTag = $tagList[$i];

  $isTagged = $db -> prepare('SELECT "'.$currTag.'" FROM "'.$table.'" WHERE name LIKE "'.$img.'"');
  $isTagged = $isTagged -> execute();
  $isTagged = $isTagged -> fetchArray();
  $isTagged = $isTagged[0];

  if ($isTagged) {
    echo "      <a href='?i=$img&t=-$currTag'><span class='usedTag'>$currTag</span></a><br>\n";
  } else {
    echo "      <a href='?i=$img&t=+$currTag'>$currTag</a><br>\n";
  }
}

echo "    </div>\n";
echo "    <div class='img'>\n";
echo "      <a href='$imageDir$img'>\n";
echo "      <img src='$imageDir$img' alt='$imageDir$img'>\n";
echo "      </a>\n";
echo "    </div>\n";
echo "  </body>\n";
echo "</html>";
?>
