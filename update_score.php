<?php
    require_once('database_functions.php');
    if (isset($_SESSION['username']) && isset($_POST['score']) && isset($_POST['gamename'])) {
        addAttempt($_SESSION['username'], $_POST['gamename'], $_POST['score']);
    }
?>