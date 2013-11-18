<?php
if ($firstTime == 1) {
  setcookie('authed', '1', time()+600);

  if (!file_exists($thumbsDir)) {
    mkdir($thumbsDir);
  }

  if (!file_exists($imageDir)) {
    mkdir($imageDir);
  }

  if (!file_exists($dbFile)) {
    touch($dbFile);
  }

  $db -> exec('CREATE TABLE "'.$table.'" (name string)');
  $db -> exec('CREATE TABLE users (name string)');
  $db -> exec('ALTER TABLE users ADD password string');
  $db -> exec('INSERT INTO users VALUES ("admin", "")');

  include 'changeSetting.php';
  changeSetting('firstTime', 0);
}
?>
