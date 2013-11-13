<?php
$name = $_FILES['file']['name'];
$name = str_replace(' ', '_', $name);
$allowedExts = array("gif", "jpeg", "jpg", "png", "webp");
$ext = explode('.', $name);
$ext = end($ext);
$filetype = $_FILES['file']['type'];
$table = 'files';

if (($filetype == 'image/gif'
     || $filetype == 'image/jpeg'
     || $filetype == 'image/jpg'
     || $filetype == 'image/pjpeg'
     || $filetype == 'image/x-png'
     || $filetype == 'image/png'
     || $filetype == 'image/webp')
     && in_array($ext, $allowedExts)) {
  if ($_FILES['file']['error'] > 0) {
    echo "Error: " . $_FILES['file']['error'] . "<br>";
  }
  if (file_exists('img/' . $name)) {
    echo "A file with that name already exists.";
  } else {
    move_uploaded_file($_FILES['file']['tmp_name'], 'img/' . $name);
    $db = new SQLite3('tags.db');
    $db -> exec('INSERT INTO "'.$table.'" (name) VALUES ("'.$name.'")');
  }
}

echo "    <form action='' method='post' enctype='multipart/form-data'>\n";
echo "      <input type='file' name='file'><br><input type='submit'>\n";
echo "    </form>\n";
?>
