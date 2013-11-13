<?php
if ($_COOKIE['authed'] == '1') {
  // HTML output
  echo "<html lang='en'>\n";
  include 'header.php';
  /*echo "  <head>\n";
  echo "    <link rel='stylesheet' type='text/css' href='style.css'>\n";
  echo "  </head>\n";*/
  echo "  <body>\n";
  echo "    <h1>\n";
  echo "      <a href=gallery.php>Home</a>\n";
  echo "    </h1><br><br>\n";
  echo "    <a href=?db=1>Generate database entries (will probably take a while the first time)</a><br>\n";
  echo "    <br>\n";
  echo "    <a href=?cleardb=1>Clear database entries</a><br>\n";
  echo "    <a href=?clearthumbs=1>Clear thumbnails</a><br>\n";
  echo "    <a href=?clearfiles=1>Delete all images</a><br>\n";
  echo "    <br>\n";
  echo "    <br>\n";
  echo "    <form>\n";
  echo "      <input type='text' name='tag' placeholder='Enter tag to add' autofocus>\n";
  echo "    </form>\n";
  echo "    <form>\n";
  echo "      <input type='text' name='delTag' placeholder='Enter tag to delete'>\n";
  echo "    </form><br>\n";
  echo "    <br>\n";
  echo "  </body>\n";
  echo "</html>";

  $clearDB = $_GET['cleardb'];
  $clearThumbs = $_GET['clearthumbs'];
  $clearFiles = $_GET['clearfiles'];
  $genDB = $_GET['db'];
  $tag = $_GET['tag'];
  $delTag = $_GET['delTag'];
  $db = new SQLite3('tags.db');
  $table = 'files';

  if ($clearFiles) {
  $files = glob('img/' . '*'); // Breaks nano syntax highlighting without the concat
    for ($i = 0; $i < count($files); $i++) {
      unlink($files[$i]);
    }
  }

  if ($tag) {
    $tag = strtolower($tag);
    $db -> exec('ALTER TABLE "'.$table.'" ADD "'.$tag.'" string');
  }

  if ($delTag) {
    $delTag = strtolower($delTag);
    $col = $db -> prepare('SELECT * FROM "'.$table.'"');
    $col = $col -> execute();
    $numCols = $col -> numColumns();
    $db -> exec('CREATE TABLE temptable(name string)');
    for ($i = 0; $i < $numCols; $i++) {
      $currColumnName = $col -> columnName($i);
      if ($currColumnName != $delTag) {
        $db -> exec('ALTER TABLE temptable ADD "'.$currColumnName.'" string');
        $db -> exec('INSERT INTO temptable SELECT "'.$currColumnName.'" FROM "'.$table.'"');
      }
    }
    $columnNames = substr($columnNames, 1);

    $db -> exec('ALTER TABLE "'.$table.'" RENAME TO temp2;
                 ALTER TABLE temptable RENAME TO "'.$table.'";
                 DROP TABLE temp2');
  }

  if ($genDB == 1) {
    $imageFiles = glob('img/' . '*.{png,gif,jpg,jpeg,webp}', GLOB_BRACE); // Breaks nano syntax highlighting without the concat
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

  if ($clearDB == 1) {
    $db -> exec('DROP TABLE "'.$table.'"');
    $db -> exec('CREATE TABLE "'.$table.'" (name string)');
  }

  if ($clearThumbs == 1) {
    $thumbs = glob('thumbs/*');
    for ($i = 0; $i < count($thumbs); $i++) {
      unlink($thumbs[$i]);
    }
  }
}
?>
