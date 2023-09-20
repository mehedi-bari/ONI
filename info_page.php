<?php
    require_once('./header.php');
    require_once('./oni_box.php');
?>

<!DOCTYPE html>
<html>
  <head>
    <title>My Info Page</title>
  </head>
  <body>
    <?php
    if (isset($_SESSION['username'])) {
      echo("You are logged in as " . $_SESSION['username'] . ".");
      foreach(getGamesList() as $gamename) {
          echo('<br>Your '.getLongGameName($gamename).' score is '.getBestAttempt($_SESSION['username'], $gamename).'.');
      }
    } else {
      echo('You have not logged-in this session. Please <a href="./login_page.php">log-in.</a>');
    }
    ?>
  </body>
</html>
