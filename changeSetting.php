<?php
function changeSetting($setting, $value) {
  $setting = '$' . $setting;
  $constantsFileName = 'constants.php';
  $constantsFileHandle = file($constantsFileName);
  for ($i = 0; $i < count($constantsFileHandle); $i++) {
    if (substr($constantsFileHandle[$i], 0, strlen($setting)) == $setting) {
      $constantsFileHandle[$i] = $setting . ' = ' . $value . ";\n";
      file_put_contents($constantsFileName, $constantsFileHandle);
      break;
    }
  }
}
?>
