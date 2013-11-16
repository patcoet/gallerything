<?php
$search = $_GET['s'];
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
} else if ($search) {
  $argument = 'SELECT name FROM "'.$table.'" WHERE name LIKE "%'.$search.'%"';
} else {
  $argument = 'SELECT name FROM "'.$table.'"';
}
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
