<?php
$page = (isset($_GET['p']) ? $_GET['p'] : null);

if (!$page) {
  $page = 1;
}
$prev = $page-1;
$next = $page+1;
$prevCheck = $prev >= 1;
$nextCheck = $next <= ceil(count($images) / $perPage);

if ($tag) {
  $extra = 't=' . $tag . '&';
} else if ($search) {
  $extra = 's=' . $search . '&';
}  else {
  $extra = '';
}
echo "<form>\n            ";
if ($prevCheck) {
  echo "<a href='?$extra" . "p=$prev'>&lt;&lt;</a> |";
} else {
  echo "<span class='deadLink'>&lt;&lt; |</span>";
}
if ($prevCheck || $nextCheck) {
  $totPages = ceil(count($images) / $perPage);
  $pageText = ($next-1) . '/' . $totPages;
  echo "<input type='text' name='p' size=1 placeholder=$pageText>";
} else {
  echo "<span class='deadLink'> 0 (0) </span>";
}
if ($nextCheck){
  echo "| <a href='?$extra" . "p=$next'>>></a>\n";
} else {
  echo "<span class='deadLink'>| >></span>\n";
}
echo "          </form>\n";
?>
