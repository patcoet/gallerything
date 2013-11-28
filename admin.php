<?php
if ($_COOKIE['authed'] == '1') { // TODO: Make admin page accessible without cookies

  // Get variables
  if (!isset($dbFile)) {
    include 'constants.php';
  }
  $clearDB = (isset($_GET['cleardb']) ? $_GET['cleardb'] : null);
  $clearThumbs = (isset($_GET['clearthumbs']) ? $_GET['clearthumbs'] : null);
  $clearFiles = (isset($_GET['clearfiles']) ? $_GET['clearfiles'] : null);
  $genDB = (isset($_GET['db']) ? $_GET['db'] : null);
  $tag = (isset($_GET['tag']) ? $_GET['tag'] : null);
  $delTag = (isset($_GET['delTag']) ? $_GET['delTag'] : null);
  $password = (isset($_POST['password']) ? $_POST['password'] : null);

  // Change admin password
  if ($password) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $db -> exec('UPDATE users SET password="'.$hashedPassword.'"');
    echo "Admin password changed to '$password'.";
  }

  // Delete images
  if ($clearFiles) {
  $files = glob($imageDir . '*');
    for ($i = 0; $i < count($files); $i++) {
      unlink($files[$i]);
    }
  }

  // Add tag
  if ($tag) {
    $db -> exec('ALTER TABLE "'.$table.'" ADD "'.$tag.'" string');
  }

  // Delete tag
  if ($delTag) {
    $delTag = strtolower($delTag);
    $col = $db -> prepare('SELECT * FROM "'.$table.'"');
    $col = $col -> execute();
    $numCols = $col -> numColumns();
    $db -> exec('CREATE TABLE temptable(name string)');
    $columnsToCopy = 'name';
    for ($i = 1; $i < $numCols; $i++) {
      $currColumnName = $col -> columnName($i);
      if ($currColumnName != $delTag) {
        $db -> exec('ALTER TABLE temptable ADD "'.$currColumnName.'" string');
        $columnsToCopy = $columnsToCopy . ', "' . $currColumnName . '"';
      }
    }
    $argument = "INSERT INTO temptable SELECT $columnsToCopy FROM $table";
    $db -> exec($argument);

    $db -> exec('ALTER TABLE "'.$table.'" RENAME TO temp2;
                 ALTER TABLE temptable RENAME TO "'.$table.'";
                 DROP TABLE temp2');
  }

  // Generate database
  if ($genDB == 1) {
    $imageFiles = glob($imageDir . '*.{png,gif,jpg,jpeg,webp}', GLOB_BRACE);
    sort($imageFiles);

    for ($i = 0; $i < count($imageFiles); $i++) {
      $currFile = substr($imageFiles[$i], 4);

      if ($imageFiles[$i] != str_replace(' ', '_', $imageFiles[$i])) {
        rename($imageFiles[$i], str_replace(' ', '_', $imageFiles[$i]));
      }

      $isInDB = $db -> prepare('SELECT name FROM "'.$table.'" WHERE name LIKE "'.$currFile.'"');
      $isInDB = $isInDB -> execute();
      $isInDB = $isInDB -> fetchArray();
      $isInDB = $isInDB[0];

/*
      $size = filesize($imageFiles[$i]);      // Slowdowns, no real utility
      $img = new Imagick($imageFiles[$i]);    // Slowdown is only significant when adding
      $width = $img -> getImageWidth();       // many files, though, so might as well
      $height = $img -> getImageHeight();     // TODO: Look at Imagick::Get* functions
*/

      if (!$isInDB) {
        $db -> exec('INSERT INTO "'.$table.'" (name) VALUES ("'.$currFile.'")');
      }
    }
  }

  // Clear database
  if ($clearDB == 1) {
    $db -> exec('DROP TABLE "'.$table.'"');
    $db -> exec('CREATE TABLE "'.$table.'" (name string)');
  }

  // Delete thumbnails
  if ($clearThumbs == 1) {
    $thumbs = glob('thumbs/*');
    for ($i = 0; $i < count($thumbs); $i++) {
      unlink($thumbs[$i]);
    }
  }

  // Change a setting in constants.php
  $settingValue = '';
  if (isset($_POST['settingvalue'])) {
    include 'changeSetting.php';
    $settingValue = $_POST['settingvalue'];
    $colonPos = strpos($settingValue, ':');
    $setting = substr($settingValue, 0, $colonPos);
    $value = substr($settingValue, $colonPos+1);
    changeSetting($setting, $value);
  }

  // Begin HTML output
  echo "<html lang='en'>\n";
  include 'header.php';
  echo "  <body>\n";
  echo "    <h1>\n";
  echo "      <a href=gallery.php>Home</a>\n";
  echo "    </h1><br><br>\n";
  echo "    <a href=?db=1>Generate database entries (might take a while)</a><br>\n";
  echo "    <br>\n";
  echo "    <a href=?cleardb=1>Clear database entries</a><br>\n";
  echo "    <a href=?clearthumbs=1>Clear thumbnails</a><br>\n";
  echo "    <a href=?clearfiles=1>Delete all images</a><br>\n";
  echo "    <br>\n";
  echo "    <br>\n";
  echo "    <form>\n";
  echo "      <input type='text' name='tag' placeholder='Enter tag to add' autofocus>\n";
  echo "    </form>\n";
  echo "    <form>\n"; // TODO: Dropdown menu of tags to delete instead
  echo "      <input type='text' name='delTag' placeholder='Enter tag to delete'>\n";
  echo "    </form>\n";
  echo "    <form action='$home' method='post'>\n";
  echo "      <input type='text' name='settingvalue' placeholder='setting:newvalue'>\n";
  echo "    </form><br>\n";
  echo "    <br>\n";
  echo "    <form action='$home' method='post'>\n";
  echo "      <input type='password' name='password' placeholder='Enter new password'>\n";
  echo "    </form>\n";
  echo "  </body>\n";
  echo "</html>";
}
?>
