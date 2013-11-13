<?php
$page = $_GET['p'];

if (!$page) {
  $page = 0;
}
$prev = $page-1;
$next = $page+1;
$prevCheck = $prev >= 0;
$nextCheck = $next < count($images) / $perPage;

if ($tag) {
  $extra = 't=' . $tag . '&';
} else if ($search) {
  $extra = 's=' . $search . '&';
} else if ($sortMethod) {
  $extra = 'sort=' . $sortMethod . '&';
}  else {
  $extra = '';
}
echo "       ";
if ($prevCheck) {
  echo "<a href='?$extra" . "p=$prev'><<</a> |";
}
if ($prevCheck || $nextCheck) {
  echo ' ' . ($next-1) . ' (' . floor(count($images) / $perPage) . ') ';
}
if ($nextCheck){
  echo "| <a href='?$extra" . "p=$next'>>></a>\n";
}
?>
