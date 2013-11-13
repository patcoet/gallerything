<?php
$search = $_GET['s'];
$sortMethod = $_GET['sort'];
$tag = $_GET['t'];

$orgTag = $tag;
if ($tag)
{
  $nextComma = strpos($tag, ',');
  if ($nextComma == false) {
    $argument = 'SELECT name FROM "'.$table.'" WHERE "'.$tag.'"=1';
  } else {
    $tag1 = substr($tag, 0, $nextComma);
    $argument = 'SELECT name FROM "'.$table.'" WHERE "'.$tag1.'"=1';
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
  $stmt = $db -> prepare($argument);
} else if ($search) {
  $stmt = $db -> prepare('SELECT name FROM "'.$table.'" WHERE name LIKE "%'.$search.'%"');
} else if (!$sortMethod) {
    $stmt = $db -> prepare('SELECT name FROM "'.$table.'" ORDER BY rowid DESC');
} else if ($sortMethod = 'dateAsc') {
    $stmt = $db -> prepare('SELECT name FROM "'.$table.'" ORDER by rowid ASC');
}
$result = $stmt -> execute();
$tempArray = array();
$i = 0;
while ($images = $result -> fetchArray()) {
  $tempArray[$i] = $images['name'];
  $i++;
}
$images = $tempArray;
?>
