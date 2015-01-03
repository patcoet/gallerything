<?php
$search = (isset($_GET['s']) ? $_GET['s'] : null);
$tag = (isset($_GET['t']) ? $_GET['t'] : null);

$orgTag = $tag;
if ($tag)
{
  $nextComma = strpos($tag, ',');
  if ($nextComma == false) {
    if (substr($tag, 0, 1) == "-") {
      $argument = 'SELECT name FROM "'.$table.'" WHERE "'.substr($tag, 1).'" IS NOT 1';
    } else {
      $argument = 'SELECT name FROM "'.$table.'" WHERE "'.$tag.'"=1';
    }
  } else {
    $tag1 = substr($tag, 0, $nextComma);
    if (substr($tag, 0, 1) == "-") {
      $argument = 'SELECT name FROM "'.$table.'" WHERE "'.substr($tag, 1).'" IS NOT 1';
    } else {
      $argument = 'SELECT name FROM "'.$table.'" WHERE "'.$tag1.'"=1';
    }
  }
  while ($nextComma != false) {
    $currComma = $nextComma;
    $tag = substr($tag, $currComma+1);
    $nextComma = strpos($tag, ',');
    if ($nextComma != false) {
      $nextTag = substr($tag, 0, $nextComma);
    } else {
      $nextTag = $tag;
    }
    $addToArgument = ' AND "'.$nextTag.'"=1';
    $argument = $argument . $addToArgument;
  }
} else if ($search) {
  $argument = 'SELECT name FROM "'.$table.'" WHERE name LIKE "%'.$search.'%"';
} else {
  $argument = 'SELECT name FROM "'.$table.'"';
}
$argument = $argument . ' ORDER BY rowid DESC';
echo "$argument";
$stmt = $db -> prepare($argument);
$result = $stmt -> execute();
$tempArray = array();
$i = 0;
while ($images = $result -> fetchArray()) {
  $tempArray[$i] = $images['name'];
  $i++;
}
$images = $tempArray;
?>
