<!DOCTYPE html>
<html>
  <head>
    <title>Log-in Page</title>
    <link rel="stylesheet" type="text/css" href="./Login.css">
    <?php require_once('./database_functions.php'); ?>
  </head>
  <body>
    <?php
        if(isset($_POST['submit'])) {
          echo('<div class="loginresult">');
          $cleaned_username = trim($_POST['username']);
          if (!empty($cleaned_username)) {
              $user = checkIfUserExists($cleaned_username);
              if ($user) {
                echo("User " . $cleaned_username . " already exists.");
              } else {
                addUser($cleaned_username);
                echo("User " . $cleaned_username . " created.");
              }
              $_SESSION['username'] = $cleaned_username;
              echo('<br><a href="./games_page.php">Continue</a><a href="./login_page.php">Log Out</a>');
          } else {
              echo('<p>Blank username is not valid.</p><br><a href="./login_page.php">Back to Login Page</a>');
          }
          echo('</div>');
        } else {
          session_unset();
          echo('<p class="entry">ENTER YOUR USERNAME</p>
                <form class="entry2" method="post">
                  Username: <input type="text" name="username" id="username"/>
                  <input type="submit" name="submit" value="Log-in"/>
                </form>
                <a class="landingpagebutton" href="./landing_page.php">Back to Landing Page</a>');
        }
    ?>
  </body>
</html>
