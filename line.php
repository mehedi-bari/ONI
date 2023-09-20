<html>
  <head>
    <?php require_once "database_functions.php"; ?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Last Five Attemps', 'Flappy Dragon', 'Maze Game', 'Dragon Game' , '2048'],
          
          <?php 
    $var = 0;
    for ($i = 0; $i < 5; $i++){
        $i++;
        echo "[$i";
        $i--;
        forEach(getGamesList() as $game) {
            if (sizeof(getAttempts('Shuib', $game, 'time'))-1 >= $i){
                echo ", ".getAttempts('Shuib', $game, 'time')[$i];
            }
            elseif ((sizeof(getAttempts('Shuib', $game, 'time')) == 0)){
                echo ", ".$var;
            }
            else {
                echo ", ".getAttempts('Shuib', $game, 'time')[sizeof(getAttempts('James', $game, 'time'))-1];
            }
        }
        if ($i == 4)
            echo "]";
        else
            echo "],";
    }
?>

        ]);

        var options = {
          title: 'Game Performance',
          curveType: 'function',
          legend: { position: 'bottom' }
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div id="curve_chart" style="width: 900px; height: 500px"></div>
  </body>
</html>
