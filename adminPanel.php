<?php
echo "    <div class='admin'>\n";
if ($logout == 1 || !(password_verify($_POST["password"], $pass) || $_COOKIE["authed"] == 1)) {
  setcookie("authed", "", time()-3600);
  echo "      <form action=$home method='post'>\n";
  echo "        <input type='password' name='password' placeholder='admin login' size=7>\n";
  echo "      </form>\n\n";
} else if (password_verify($_POST["password"], $pass) || $_COOKIE["authed"] == 1) {
  setcookie("authed", "1", time()+3600);
  echo "      <a href='admin.php'>Admin</a><br>\n";
  echo "      <a href='$home?logout=1'>Log out</a><br>\n";
}
echo "    </div>\n\n";
?>
