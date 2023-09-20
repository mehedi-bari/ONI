<?php
    $GLOBALS['host'] = "dbhost.cs.man.ac.uk";
    $GLOBALS['username'] = "s13506jc";
    $GLOBALS['password'] = "TopSecretPassword";
    $GLOBALS['dbname'] = "2021_comp10120_m5";

    $pdonodb = new PDO("mysql:host=".$GLOBALS['host'], $GLOBALS['username'], $GLOBALS['password']);
    $sql = "CREATE DATABASE IF NOT EXISTS ".$GLOBALS['dbname'];
    $pdonodb->query($sql);
    
    $GLOBALS['pdo'] = new PDO("mysql:host=".$GLOBALS['host'].";dbname=".$GLOBALS['dbname'], $GLOBALS['username'], $GLOBALS['password']);

    $sql = "CREATE TABLE IF NOT EXISTS users (
    userId INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(40) NOT NULL UNIQUE)";
    $GLOBALS['pdo']->query($sql);
    
    $sql = "CREATE TABLE IF NOT EXISTS attempts (
    attemptId INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gamename VARCHAR(40) NOT NULL,
    username VARCHAR(40) NOT NULL,
    score BIGINT(30) NOT NULL,
    time BIGINT(12) NOT NULL)";
    $GLOBALS['pdo']->query($sql);

function addUser($username) {
    $sql = "INSERT INTO users (username) VALUES (:username)";
    $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->execute([
        'username' => $username
    ]);
}

function removeUser($username) {
    $sql = "DELETE FROM users WHERE username='$username'";
    $GLOBALS['pdo']->query($sql);
}

function setScore($username, $newvalue, $gamenumber) {
  $sql = "UPDATE users SET score_game$gamenumber=$newvalue WHERE username='$username'";
  $GLOBALS['pdo']->query($sql);
}

function adjustScore($username, $change, $gamenumber) {
  $sql = "UPDATE users SET score_game$gamenumber=score_game$gamenumber+$change WHERE username='$username'";
  $GLOBALS['pdo']->query($sql);
}

function dropTable($name) {
  $sql = "DROP TABLE $name";
  $GLOBALS['pdo']->query($sql);
}

function checkIfUserExists($username) {
  $sql = "SELECT * FROM users WHERE username = :username";
  $stmt = $GLOBALS['pdo']->prepare($sql);
  $stmt->execute(['username' => $username]);
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  if(count($stmt->fetchAll()) == 0) {
      return false;
  } else {
      return true;
  };
}

function getAllUsernames() {
  $sql = "SELECT username FROM users";
  $stmt = $GLOBALS['pdo']->prepare($sql);
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  return array_map('implode', $stmt->fetchAll());
}

function getGamesList() {
	return array_map('basename', glob("games/*", GLOB_ONLYDIR));
}

function getGameFolder($gamename) {
    return "./games/".$gamename."/";
}
  
function getLongGameName($gamename) {
	$filelocation = getGameFolder($gamename)."longname.txt";
	if (file_exists($filelocation)) {
    	$namefile = fopen($filelocation, "r");
    	$longname = fgets($namefile);
    	fclose($namefile);
    	return $longname;
	} else {
	    return $gamename." (placeholder)";
	}
}

function getGameURL($gamename) {
	return getGameFolder($gamename)."game.html";
}

function getGameScreenshotPath($gamename) {
    $screenshot_path = getGameFolder($gamename)."screenshot.png";
    if (file_exists($screenshot_path)) {
        return $screenshot_path;
    } else {
        return "./images/placeholder.png";
    }
}

function addAttempt($username, $gamename, $score) {
    $sql = "INSERT INTO attempts (username, gamename, score, time) VALUES (:username, :gamename, :score, :time)";
    $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->execute([
        'username' => $username,
        'gamename' => $gamename,
        'score' => $score,
        'time' => time()
    ]);
}

function getAttempts($username, $gamename, $sort_column) {
  $sql = "SELECT score FROM attempts WHERE username = :username AND gamename = :gamename ORDER BY ".$sort_column." DESC";
  $stmt = $GLOBALS['pdo']->prepare($sql);
  $stmt->execute(['username' => $username, 'gamename' => $gamename]);
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  return array_map('implode', $stmt->fetchAll());
}

function getBestAttempt($username, $gamename) {
    $attempts = getAttempts($username, $gamename, 'score');
    if(count($attempts) == 0) {
        return 0;
    } else {
        return $attempts[0];
    }
}

function getOverallScore($username) {
    $overallscore = 0;
    foreach(getGamesList() as $gamename) {
        $overallscore += getBestAttempt($username, $gamename);
    }
    return $overallscore;
}

function getUserListSortedByScore($gamename) {
  if($gamename == 'overall'){
    $sortedusersarray = [];
    foreach (getAllUsernames() as $username) {
        $sortedusersarray[$username] = getOverallScore($username);
    }
    arsort($sortedusersarray);
    $sortedusersarray = array_keys($sortedusersarray);
  } else {
      $sql = "SELECT username FROM attempts WHERE gamename = :gamename ORDER BY score DESC";
      $stmt = $GLOBALS['pdo']->prepare($sql);
      $stmt->execute(['gamename' => $gamename]);
      $stmt->setFetchMode(PDO::FETCH_ASSOC);
      $sortedusersarray = array_unique(array_map('implode', $stmt->fetchAll()));
      foreach (getAllUsernames() as $username) {
          if(!in_array($username, $sortedusersarray)) {
              $sortedusersarray[] = $username;
          }
      }
  }
  return $sortedusersarray;
}

session_start();

if (isset($_SESSION['username'])) {
    if(!checkIfUserExists($_SESSION['username'])) {
        unset($_SESSION['username']);
    };
}
?>
<html>
    <head>
        <script>
        	function updateDatabaseScore(score) {
        	    const xhr = new XMLHttpRequest();
        	    xhr.open("POST", "./update_score.php");
        	    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        	    xhr.send("score="+score.toString()+"&gamename=<?php if (isset($_GET['gamename'])) { echo($_GET['gamename']); } else { echo('none'); } ?>");
        	    <?php if (str_contains($_SERVER['PHP_SELF'], 'container')) { echo('oniReceiveScore(score);'); } ?>
            }
        </script>
    </head>
</html>