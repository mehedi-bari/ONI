<?php
	require_once('./database_functions.php');
?>

<!DOCTYPE html>
<html>
	<head>
	    <link rel="stylesheet" type="text/css" href="./OniMainPages.css">
	</head>
    <body>
        <div class="about-section">
            <h1 class="header_title">The Challenges of Oni</h1>
            <a class="button" <?php if (str_contains($_SERVER['PHP_SELF'], 'games_page')){echo('disabled style="background-color: #7d3745; color: #b8b8b8;"');}else{echo('href="./games_page.php"');}?>>Games Page</a>
            <a class="button" <?php if (str_contains($_SERVER['PHP_SELF'], 'about_page')){echo('disabled style="background-color: #7d3745; color: #b8b8b8;"');}else{echo('href="./about_page.php"');}?>>About Page</a>
            <a class="button" <?php if (str_contains($_SERVER['PHP_SELF'], 'leaderboard_page')){echo('disabled style="background-color: #7d3745; color: #b8b8b8;"');}else{echo('href="./leaderboard_page.php"');}?>>Leaderboard</a>
            <?php
                if (isset($_SESSION['username'])) {
                    echo('<span style="margin-right: 7px; margin-left: 40px;">LOGGED IN AS: '.$_SESSION['username'].'</span><a class="button"');
                    if (str_contains($_SERVER['PHP_SELF'], 'info_page')) {
                        echo(' disabled style="background-color: #7d3745; color: #b8b8b8;"');
                    }
                    else {
                        echo(' href="./info_page.php"');
                    }
                    echo('>My Info</a><a href="./login_page.php" class="button">Log Out</a>');
            
                } else {
                    echo('<a href="./login_page.php" class="button" style="margin-left: 40px;">Log In</a>');
                }
            ?>
        </div>  
    </body>
</html>