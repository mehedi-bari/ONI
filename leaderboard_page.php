<?php
    require_once('./header.php');
    require_once('./oni_box.php');
?>
    
<!DOCTYPE html>
<html>
    <head>
        <title>Leaderboard</title>
    </head>
    <body>
        <table>
          <tr>
            <th>Name</th>
            <?php 
                foreach(getGamesList() as $gamename) {
                    echo('<th><div class="tableheading"><form method="post" action=""><input name="sorting" type="hidden" value="'.$gamename.'"/><input value="'.getLongGameName($gamename).'" type="submit"/></form></div></th>');
                }
            ?>
            <th>
                <div class="tableheading">
                    <form method="post" action="">
                        <input name="sorting" type="hidden" value="overall"/>
                        <input value="Overall" type="submit"/>
                    </form>
                </div>
            </th>
          </tr>
        <?php
        if (isset($_POST['sorting'])) {
            $sorting = $_POST['sorting'];
        } else {
            $sorting = 'overall';
        }
        if (count(getUserListSortedByScore('overall')) > 2) {
            $first_place = getUserListSortedByScore('overall')[0];
            $second_place = getUserListSortedByScore('overall')[1];
            $third_place = getUserListSortedByScore('overall')[2];
        }
        foreach (getUserListSortedByScore($sorting) as $user) {
            echo('<tr>');
            echo('<td>');
            if ($user == $first_place) {
                echo('<img src="./images/medal1.png" width="15" height="15"> ');
            }
            else if ($user == $second_place) {
                echo('<img src="./images/medal2.png" width="15" height="15"> ');
            }
            else if ($user == $third_place) {
                echo('<img src="./images/medal3.png" width="15" height="15"> ');
            }
            echo($user.'</td>');
            foreach(getGamesList() as $gamename) {
                echo('<td>'.getBestAttempt($user, $gamename).'</td>');
            }
            echo('<td>'.getOverallScore($user).'</td>');
            echo('</tr>');
        }
        ?>
        </table>
    </body>
</html>