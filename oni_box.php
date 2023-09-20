<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="./OniBox.css">
    </head>
    <body>
        <div id="oni_box">
            <div class="oni-container">
                <div class="oni_left">
                    <img src="images/oni.webp" alt="Oni">
                    <img src="images/speech1.webp">
                </div>
                <div class="oni-text">
                    <p id="oni_words">Text!</p>
                    <a id="oni_close" onclick="hideOni()" href="javascript:void(0);">Go away, Oni!</a>
                </div>
                <div class="oni_right">
                    <img src="images/speech2.webp">
                </div>
            </div>
        </div>
        <script>
            function updateOniText(text) {
                document.getElementById("oni_words").innerHTML = text;
                document.getElementById("oni_box").style.display = "block";
                document.getElementById("game-iframe").contentWindow.focus();
            }
            
            function oniReceiveScore(score) {
                updateOniText("Only SCORE points? My grandma could do better...".replace('SCORE', score));
            }
            
            function hideOni() {
                document.getElementById("oni_box").style.display = "none";
                document.getElementById("game-iframe").contentWindow.focus();
            }
            
            updateOniText("Hmm...");
            let oniPresent = true;
        </script>
        <?php
            function changeOniText($text) {
                echo('<script>updateOniText("'.$text.'")</script>');
            }
            
            function oniArrayFromFile($filepath) {
                if (file_exists($filepath)) {
                	$commentsfile = fopen($filepath, "r");
                	$commentsarray = explode("\n", fread($commentsfile, filesize($filepath)));
                	fclose($commentsfile);
                	return $commentsarray;
                } else {
                    return ["I'm speechless."];
                }
            }
            
            function oniRandomCommentFromFile($filepath) {
                $oniComments = oniArrayFromFile($filepath);
                $rand_comment = array_rand($oniComments);
                return $oniComments[$rand_comment];
            }
              
            
            if (str_contains($_SERVER['PHP_SELF'], 'landing')) {
                changeOniText(oniRandomCommentFromFile('OniComments_LandingPage.txt'));
            } else if (!isset($_SESSION['username'])) {
                echo('<script>updateOniText("Log in to face me!")</script>');
            } else {
                if (str_contains($_SERVER['PHP_SELF'], 'leaderboard')) {
                    $top_player = getUserListSortedByScore('overall')[0];
                    if ($_SESSION['username'] == $top_player) {
                        changeOniText('I see you reached the top...<br>But you are still no match for me!');
                    }
                    else {
                        $score_difference = getOverallScore($top_player) - getOverallScore($_SESSION['username']);
                        changeOniText('Ha! You are still '.$score_difference.' points away from the lead.<br>You suck at videogames!');
                    }
                }
                else if (str_contains($_SERVER['PHP_SELF'], 'info')) {
					changeOniText(str_replace('UNAME', $_SESSION['username'], oniRandomCommentFromFile("OniComments_Info.txt")));
                }
                else if (str_contains($_SERVER['PHP_SELF'], 'container') && isset($_GET["gamename"])) {
					$previousbest = getBestAttempt($_SESSION['username'], $_GET["gamename"]);
					if ($previousbest == 0) {
						changeOniText(oniRandomCommentFromFile(getGameFolder($_GET['gamename'])."OniTutorial.txt"));
					} else {
						changeOniText(str_replace('PBEST', $previousbest, oniRandomCommentFromFile(getGameFolder($_GET['gamename'])."OniTaunts.txt")));
					}
				}
            }
        ?>
    </body>
</html>
