<?php
    require_once('./header.php');
    require_once('./database_functions.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <title><?php echo(getLongGameName($_GET['gamename'])); ?></title>
    </head>
    <body>
        <div class="game-container">
            <iframe id="game-iframe" src="<?php echo(getGameURL($_GET['gamename'])); ?>" class="Game" scrolling="no" frameBorder="0" onload="this.contentWindow.focus()"></iframe>
        </div>
        <?php require_once('./oni_box.php'); ?>
    </body>
</html>