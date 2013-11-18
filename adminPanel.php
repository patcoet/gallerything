<?php
// Get variables
$pass = $db -> prepare('SELECT password FROM users');
$pass = $pass -> execute();
$pass = $pass -> fetchArray();
$pass = $pass[0];

$logout = '';
if (isset($_GET['logout'])) {
  $logout = $_GET['logout'];
}

$authed = '';
if (isset($_COOKIE['authed'])) {
  $authed = $_COOKIE['authed'];
}

$submittedPassword = '';
if (isset($_POST['submittedPassword'])) {
  $submittedPassword = $_POST['submittedPassword'];
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

// Reset $table, keeping filenames and dropping all tags
if (isset($_POST['resetDatabase'])) {
  $imageFiles = glob($imageDir . '*.{png,gif,jpg,jpeg,webp}', GLOB_BRACE);
  sort($imageFiles);

  for ($i = 0; $i < count($imageFiles); $i++) {
    $currFilename = substr($imageFiles[$i], strlen($imageDir));

    $db -> exec('CREATE TABLE temptable (name string);
                 INSERT INTO temptable (name) SELECT name FROM "'.$table.'";
                 DROP TABLE "'.$table.'";
                 ALTER TABLE temptable RENAME TO "'.$table.'"');
  }
}

// Set/unset cookie
$passwordIsCorrect = password_verify($submittedPassword, $pass);
if ($passwordIsCorrect == true) {
  setcookie('authed', '1', time()+3600);
}
if ($logout) {
  setcookie('authed', '', time()-3600);
}

// Set new admin password
$newPassword = '';
if (isset($_POST['newPassword'])) {
  $newPassword = $_POST['newPassword'];
}

// Add a tag
$tagToAdd = '';
if (isset($_POST['tagToAdd'])) {
  $tagToAdd = $_POST['tagToAdd'];
}
if ($tagToAdd) {
  $db -> exec('ALTER TABLE "'.$table.'" ADD "'.$tagToAdd.'" string');
}

// Remove a tag
$tagToRemove = '';
if (isset($_POST['tagToRemove'])) {
  $tagToRemove = $_POST['tagToRemove'];
  $col = $db -> prepare('SELECT * FROM "'.$table.'"');
  $col = $col -> execute();
  $numberOfColumns = $col -> numColumns();
  $db -> exec('CREATE TABLE temptable(name string)');
  for ($i = 0; $i < $numberOfColumns; $i++) {
    $currColumnName = $col -> columnName($i);
    if ($currColumnName != $tagToRemove) {
      $db -> exec('ALTER TABLE temptable ADD "'.$currColumnName.'" string');
      $db -> exec('INSERT INTO temptable SELECT "'.$currColumnName.'" FROM "'.$table.'"');
    }
  }
  $db -> exec('ALTER TABLE "'.$table.'" RENAME TO temp2;
               ALTER TABLE temptable RENAME TO "'.$table.'";
               DROP TABLE temp2');
}

// Set new admin password
if ($newPassword) {
  $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
  $db -> exec('UPDATE users SET password="'.$hashedPassword.'"');
  echo "Admin password changed to '$newPassword'.";
}

// Begin HTML output
echo "    <div class='admin'>\n";
if (($firstTime == 1 || $authed == 1 || $passwordIsCorrect == true) && $logout != 1) {
  echo "      <a href='admin.php'>Admin</a><br>\n";
  include 'upload.php';
  echo "      <form action='$home' method='post'>\n";
  echo "        <input type='text' name='settingvalue' placeholder='setting:newvalue'>\n";
  echo "      </form>\n";
  echo "      <form action='$home' method='post'>\n";
  echo "        <input type='submit' name='resetDatabase' value='Reset database'>\n";
  echo "      </form>\n";
  //echo // Set all tags to 0
  //echo // Remove all tags
  //echo // Delete thumbnails
  //echo // Delete images
  echo "      <form action='$home' method='post'>\n";
  echo "        <input type='text' name='tagToAdd' placeholder='Add tag'>\n";
  echo "      </form>\n";
  echo "      <form action='$home' method='post'>\n";
  echo "        <input type='text' name='tagToRemove' placeholder='Remove tag'>\n";
  echo "      </form>\n";
  echo "      <form action='$home' method='post'>\n";
  echo "        <input type='password' name='newPassword' placeholder='New password'>\n";
  echo "      </form>\n";
  echo "      <a href='$home?logout=1'>Log out</a><br>\n";
} else {
  echo "      <form action='$home' method='post'>\n";
  echo "        <input type='password' name='submittedPassword' placeholder='admin login' size=7>\n";
  echo "      </form>\n\n";
}
echo "    </div>\n\n";
?>
