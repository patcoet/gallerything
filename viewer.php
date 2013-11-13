<?php
//header('Content-type: text/html; charset=UTF-8');
// TODO: search prev next

// Get the filename of the image we're displaying
$img = $_GET['i'];

// Set or unset a tag
$tag = $_GET['t'];
if ($tag) {
  if (substr($tag, 0, 1) == "-") {
    $op = 0;
  } else {
    $op = 1;
  }
  $tag = substr($tag, 1);
  //$stmt = $db -> prepare('UPDATE "'.$table.'" SET "'.$tag.'"="'.$op.'" WHERE name="'.$img.'"');
  $db -> exec('UPDATE "'.$table.'" SET "'.$tag.'"="'.$op.'" WHERE name="'.$img.'"');
  //$stmt -> execute();
}

// SQLite DB stuff
include 'constants.php';
/*$db = new SQLite3('tags.db');
$table = 'files';*/

// Get a list of available tags
$stmt = $db -> prepare('SELECT * FROM "'.$table.'"');
$result = $stmt -> execute();
$tagList = array();
for ($i = 1; $i < $result -> numColumns(); $i++) { // Note: the 1 assumes that column 1 is the first tag column
  $tagList[$i-1] = $result -> columnName($i);
}
sort($tagList);

// Get name of previous and next image, if they exist
$sortMethod = $_GET['sort'];
if ($sortMethod == 'dateAsc') {
  $nextOp = 1;
  $prevOp = -1;
} else {
  $nextOp = -1;
  $prevOp = 1;
}
$curr = $db -> prepare('SELECT rowid FROM "'.$table.'" WHERE name="'.$img.'"');
$curr = $curr -> execute();
$curr = $curr -> fetchArray();
$curr = $curr[0];
$rowID = $curr + $prevOp;
$prev = $db -> prepare('SELECT name FROM "'.$table.'" WHERE rowid="'.$rowID.'"');
$prev = $prev -> execute();
$prev = $prev -> fetchArray();
$prev = $prev[0];
$rowID = $curr + $nextOp;
$next = $db -> prepare('SELECT name FROM "'.$table.'" WHERE rowid="'.$rowID.'"');
$next = $next -> execute();
$next = $next -> fetchArray();
$next = $next[0];


// Begin HTML output
echo "<html>\n";
include 'header.php';
/*echo "  <head>\n";
echo "    <link rel='stylesheet' type='text/css' href='style.css'>\n";
echo "  </head>\n";*/
echo "  <body>\n";
echo "    <div class='menu'>\n";
echo "      <img src='separator.png'><br>\n";
echo "      <h1>\n";
echo "        <a href='gallery.php'>A Gallery</a>\n";
echo "      </h1><br>\n";
echo "      <h2>\n";

if ($sortMethod) {
  $addArg = '&sort=' . $sortMethod;
}
if ($prev) {
  $prev = $prev . $addArg;
  echo "        <a href='?i=$prev'><< Prev</a>";
}
if ($prev && $next) {
  echo " | ";
}
if ($next) {
  $next = $next . $addArg;
  echo "<a href='?i=$next'>Next >></a>\n";
}

echo "      </h2><br>\n";
echo "      <img src='separator.png'><br>\n";

/*$stmt = $db -> prepare('SELECT * FROM "'.$table.'" WHERE name LIKE "%'.$img.'%"');
$result = $stmt -> execute();
$file = $result -> fetchArray();
*/ // Not used?

for ($i = 0; $i < count($tagList); $i++) {
  $currTag = $tagList[$i];
  echo "      <a href='?i=$img&t=-$currTag'>-</a> ";
  echo "<a href='?i=$img&t=+$currTag'>";

  $isTagged = $db -> prepare('SELECT "'.$currTag.'" FROM "'.$table.'" WHERE name LIKE "'.$img.'"');
  $isTagged = $isTagged -> execute();
  $isTagged = $isTagged -> fetchArray();
  $isTagged = $isTagged[0];

  if ($isTagged) {
    echo "<span CLASS='usedTag'>";
  }
  echo "$currTag";
  if ($isTagged) {
    echo "</span>";
  }
  echo "</a></br>\n";
}

if ($_COOKIE['authed'] == '1') {
  echo "      <br>\n";
  echo "      <br>\n";
  echo "      <br>\n";
  echo "      <img src='separator.png'><br>\n";
  echo "      <a href='gallery.php?del=$img'>Delete file</a><br>\n";
}

echo "    </div>\n";
echo "    <div class='img'>\n";
echo "      <a href=img/$img>\n";
echo "      <img src=img/$img>\n";
echo "      </a>\n";
echo "    </div>\n";
echo "  </body>\n";
echo "</html>";
?>
