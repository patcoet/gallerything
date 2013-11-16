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
}  else {
  $extra = '';
}
if ($prevCheck) {
  echo "<a href='?$extra" . "p=$prev'>&lt;&lt;</a> |";
} else {
  echo "<span class='deadLink'>&lt;&lt; |</span>";
}
if ($prevCheck || $nextCheck) {
  echo ' ' . ($next-1) . ' (' . floor(count($images) / $perPage) . ') ';
} else {
  echo "<span class='deadLink'> 0 (0) </span>";
}
if ($nextCheck){
  echo "| <a href='?$extra" . "p=$next'>>></a>\n";
} else {
  echo "<span class='deadLink'>| >></span>\n";
}
echo "        <form action=$home>\n";
echo "          <input type='text' name='p' size='1' placeholder=' ####'>\n";
echo "        </form>\n";
echo "          ";
?>
