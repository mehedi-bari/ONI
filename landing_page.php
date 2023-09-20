<?php
    require_once('./database_functions.php');
    require_once('./oni_box.php');
?>

<!DOCTYPE html>
<html>
    <head>
    	<title>Challenges of Oni</title>
    	<link rel="stylesheet" type="text/css" href="./LandingPage.css">
    </head>
    <body>
    	<header>
    		<div class="main">
    			<ul>
    				<li><a href="./about_page.php">ABOUT</a></li>
    				<li><a href="./leaderboard_page.php">LEADERBOARD</a></li>
    		        <?php
    		        if (isset($_SESSION['username'])) {
    		            echo("<li style='margin-left: 20pt;'>LOGGED IN AS: ".$_SESSION['username']."</li>");
    				}
    				?>
    			</ul>
    		</div>
    
    		<div class="title">
    			<h1>CHALLENGES OF ONI</h1>
    		</div>
    
    		<div class="button">
    		    <?php
    		    if (isset($_SESSION['username'])) {
    		        $button_destination = "./games_page.php";
    		    } else {
    		        $button_destination = "./login_page.php";
    		    }
    		    echo('<a id="button1" class="btn" href="'.$button_destination.'">BEGIN QUEST</a>');
    		    ?>
    		</div>
    	</header>
    </body>
</html>
