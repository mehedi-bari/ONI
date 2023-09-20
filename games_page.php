<?php
    require_once('./header.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Games Page</title>
    </head>
    <body>
    <div class="games_page_grid">
    	<?php
    		foreach (getGamesList() as $gamename) {
    			echo('<a href="./game_container_page.php?gamename='.$gamename.'" class="button"><img src="'.getGameScreenshotPath($gamename).'" width="200" height="112"><br>'.getLongGameName($gamename).'</a>');
    		}
    	?>
	</div>
    </body>
</html>