<?php
if (isset($_FILES['file'])) {
  $fileHandle = $_FILES['file'];
  $error      = $fileHandle['error'];
  $fileType   = $fileHandle['type'];
  $fileName   = $fileHandle['name'];

  $allowedExts = array("gif", "jpeg", "jpg", "png", "webp");
  $ext = explode('.', $fileName);
  $ext = end($ext);
  $fileName =  str_replace(' ', '_', $fileName);

  if ((   $fileType == 'image/gif'
       || $fileType == 'image/jpeg'
       || $fileType == 'image/jpg'
       || $fileType == 'image/pjpeg'
       || $fileType == 'image/x-png'
       || $fileType == 'image/png'
       || $fileType == 'image/webp')
       && in_array($ext, $allowedExts)) {
    if ($error > 0) {
      $feedback = $feedback . "Error: $error<br>\n";
    }
    if (file_exists($imageDir . $fileName)) {
      $feedback = $feedback . "A file with that name already exists.";
    } else {
      move_uploaded_file($fileHandle['tmp_name'], $imageDir . $fileName);
      $db -> exec('INSERT INTO "'.$table.'" (name) VALUES ("'.$fileName.'")');
      include 'thumbGen.php';
      $feedback = $feedback . "File saved as $imageDir$fileName.";
    }
  }
}

echo "      <form action=$home method='post' enctype='multipart/form-data'>\n";
echo "        <input type='file' name='file'><br>\n";
echo "        <input type='submit'>\n";
echo "      </form>\n";
?>
