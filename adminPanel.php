<?php
// Get variables
$pass = $db -> prepare('SELECT password FROM users');
$pass = $pass -> execute();
$pass = $pass -> fetchArray();
$pass = $pass[0];

$logout = (isset($_GET['logout']) ? $_GET['logout'] : null);
$authed = (isset($_COOKIE['authed']) ? $_COOKIE['authed'] : null);
$submittedPassword = (isset($_POST['submittedPassword']) ? $_POST['submittedPassword'] : null);

// Set/unset cookie
$passwordIsCorrect = password_verify($submittedPassword, $pass);
if ($passwordIsCorrect == true) {
  setcookie('authed', '1', time()+3600);
}
if ($logout) {
  setcookie('authed', '', time()-3600);
}

// Begin HTML output
echo "    <div class='admin'>\n";
if (($firstTime == 1 || $authed == 1 || $passwordIsCorrect == true) && $logout != 1) {
  echo "      <a href='admin.php'>Admin</a><br>\n";
  include 'upload.php';
  echo "      <a href='$home?logout=1'>Log out</a><br>\n";
} else {
  echo "      <form action='$home' method='post'>\n";
  echo "        <input type='password' name='submittedPassword' placeholder='admin login' size=7>\n";
  echo "      </form>\n\n";
}
echo "    </div>\n\n";
?>
