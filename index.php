<?php
include "db.php";
session_start();
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $data = $conn->query("SELECT resultados.id_jugador, jugadores.nombre_jugador, 
        count(*) as PJ,
        SUM(CASE WHEN resultado = 1 THEN 1 ELSE 0 END) as PG,
        SUM(CASE WHEN resultado = 0 THEN 1 ELSE 0 END) as PP,
        SUM(CASE WHEN resultado = 2 THEN 1 ELSE 0 END) as PE,
        ifnull(format((SUM(CASE WHEN resultado = 1 THEN 1 ELSE 0 END) / count(*) )*100,0),0) as '%G',
        max(resultados.fecha_hora) as ULT_PJ
        from resultados, jugadores 
        where resultados.id_jugador = jugadores.id
        group by id_jugador order by PG desc, '%G' desc;")->fetchAll();
        $jugadores = $conn->query("SELECT id, nombre_jugador
        from jugadores
        order by nombre_jugador;")->fetchAll();
    if(isset($_POST['player_1']) && isset($_POST['player_2'])){                               
        $player_1 = $_POST['player_1'];
        $player_2 = $_POST['player_2'];
        $stmt = $conn->prepare("SELECT ifnull(sum(p1_PG),0) as p1_PG, 
                ifnull(sum(p1_PP),0) as p1_PP,
                ifnull(sum(p1_PE),0) as p1_PE,
                ifnull(sum(p2_PG),0) as p2_PG,
                ifnull(sum(p2_PP),0) as p2_PP,
                ifnull(sum(p2_PE),0) as p2_PE,
                ifnull(sum(p1_PG + p1_PP + p1_PE),0) as PJ
        FROM ( 	SELECT 
                SUM(CASE WHEN resultado = 1 THEN 1 ELSE 0 END) as p1_PG, 
                SUM(CASE WHEN resultado = 0 THEN 1 ELSE 0 END) as p1_PP, 
                SUM(CASE WHEN resultado = 2 THEN 1 ELSE 0 END) as p1_PE,
                resultados.equipo as p1_equipo, 
                resultados.fecha_hora as p1_fecha_hora
                from resultados 
                where resultados.id_jugador = :player_1
                group by p1_fecha_hora, p1_equipo ) AS p1
        JOIN ( 	SELECT 
                SUM(CASE WHEN resultado = 1 THEN 1 ELSE 0 END) as p2_PG, 
                SUM(CASE WHEN resultado = 0 THEN 1 ELSE 0 END) as p2_PP, 
                SUM(CASE WHEN resultado = 2 THEN 1 ELSE 0 END) as p2_PE,
                resultados.equipo as p2_equipo, 
                resultados.fecha_hora as p2_fecha_hora
                from resultados
                where resultados.id_jugador = :player_2
                group by p2_fecha_hora, p2_equipo) AS p2
                ON p1.p1_fecha_hora=p2.p2_fecha_hora
                where p1.p1_equipo <> p2.p2_equipo");
            $stmt->execute(['player_1' => $player_1, 'player_2' => $player_2]); 
            $compara = $stmt->fetchAll();
        $stmtf = $conn->prepare("SELECT ifnull(sum(p1_PG),0) as PG, 
                ifnull(sum(p1_PP),0) as PP,
                ifnull(sum(p1_PE),0) as PE,
                ifnull(sum(p1_PG + p1_PP + p1_PE),0) as PJ
        FROM ( 	SELECT 
                SUM(CASE WHEN resultado = 1 THEN 1 ELSE 0 END) as p1_PG, 
                SUM(CASE WHEN resultado = 0 THEN 1 ELSE 0 END) as p1_PP, 
                SUM(CASE WHEN resultado = 2 THEN 1 ELSE 0 END) as p1_PE,
                resultados.equipo as p1_equipo, 
                resultados.fecha_hora as p1_fecha_hora
                from resultados 
                where resultados.id_jugador = :player_1
                group by p1_fecha_hora, p1_equipo ) AS p1
        JOIN ( 	SELECT 
                SUM(CASE WHEN resultado = 1 THEN 1 ELSE 0 END) as p2_PG, 
                SUM(CASE WHEN resultado = 0 THEN 1 ELSE 0 END) as p2_PP, 
                SUM(CASE WHEN resultado = 2 THEN 1 ELSE 0 END) as p2_PE,
                resultados.equipo as p2_equipo, 
                resultados.fecha_hora as p2_fecha_hora
                from resultados
                where resultados.id_jugador = :player_2
                group by p2_fecha_hora, p2_equipo) AS p2
                ON p1.p1_fecha_hora=p2.p2_fecha_hora
                where p1.p1_equipo=p2.p2_equipo");
            $stmtf->execute(['player_1' => $player_1, 'player_2' => $player_2]); 
            $friend = $stmtf->fetchAll();
        }
        else{
            echo '';
        }
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Fútbol Luigi - Resultados y estadísticas</title>
	<!-- Compiled and minified CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
	<!-- Icons -->
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- datatables-->
    <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" rel="stylesheet">
    <style>
        table.dataTable thead th, table.dataTable thead td {
            padding: 10px 18px;
            border-bottom: 1px solid #1110;
            }
        table.dataTable.no-footer {
            border-bottom: 1px solid #1110;
            }      
        table#tabla.dataTable tbody tr:hover {
            background-color: #dbe4ff;
            } 
        table#tabla.dataTable tbody tr:hover > .sorting_1 {
            background-color: #dbe4ff;
            } 
        .tabs .tab a{
            color:#989898;
        } 
        .tabs .tab a:hover {
            background-color:#ededfb;
            color:#007fca;
        } 
        .tabs .tab a:focus.active {
            background-color:#e3e2ff;
            color:#007fca;
        }
        .tabs .tab a.active {
            background-color:#e3e2ff;
            color:#007fca;
        }
        .tabs .indicator {
            background-color:#007fca;
        }   
    </style>
</head>
<body>
<nav class="blue darken-4" role="navigation">
    <div class="nav-wrapper container"><a id="logo-container" href="index.php" class="brand-logo">Fútbol Luigi</a>
      <ul class="right hide-on-med-and-down">
		<li class="active"><a href="index.php">Resultados</a></li>
		<li><a href="fechas.php">Fechas</a></li>
		<li><a href="jugadores.php">Jugadores</a></li>
        <?php         
            if(!isset($_SESSION['uname'])){                               
                echo '<li><a href="login.php">Log In</a></li>';
            }
            else{
                echo '<li><a href="logout.php">' . $_SESSION['uname'] . ' (Logout)</a></li>';
            }
        ?>
      </ul>
      <ul id="nav-mobile" class="sidenav">
		<li class="active"><a href="index.php">Resultados</a></li>
		<li><a href="fechas.php">Fechas</a></li>
		<li><a href="jugadores.php">Jugadores</a></li>
        <?php         
            if(!isset($_SESSION['uname'])){                               
                echo '<li><a href="login.php">Log In</a></li>';
            }
            else{
                echo '<li><a href="logout.php">' . $_SESSION['uname'] . ' (Logout)</a></li>';
            }
        ?>
      </ul>
      </ul>
      <a href="#" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">menu</i></a>
    </div>
  </nav>

  <div class="section no-pad-bot" id="index-banner">
    <div class="container">
         <div class="row">
            <div class="col s12">
                <ul class="tabs">
                    <li class="tab col s3"><a class="active" href="#tabla_resultados">Tabla General</a></li>
                    <li class="tab col s3"><a href="#player_vs_player">Player vs. Player</a></li>
                </ul>
            </div>
            <div id="tabla_resultados" class="col s12">
                <table class="highlight" id="tabla" class="display hover" style="width:100%; margin-top: 24px;">
                    <thead>
                    <tr>
                        <th>Jugador</th>
                        <th>PJ</th>
                        <th>PG</th>
                        <th>PP</th>
                        <th>PE</th>
                        <th>% G</th>
                        <th>Último PJ</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($data as $row) {
                                echo '<tr>';
                                echo "<td>" . $row['nombre_jugador']. "</td>";
                                echo "<td>" . $row['PJ']. "</td>";
                                echo "<td>" . $row['PG']. "</td>";
                                echo "<td>" . $row['PP']. "</td>";
                                echo "<td>" . $row['PE']. "</td>";
                                echo "<td>" . $row['%G']. "</td>";
                                echo "<td>" . date_format(date_create($row['ULT_PJ']),"d/m/Y")  . "</td>";
                                echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div id="player_vs_player" class="col s12" style="margin-top: 24px;">
            <form action="index.php#player_vs_player" method="post" >
                    <div class="form-group col s12 m6">                
                        <label>Player 1</label>
                        <select class="browser-default" name="player_1">
                        <option selected="true" disabled="disabled">Elija un Jugador..</option>    
                            <?php
                            foreach ($jugadores as $row) {
                                echo '<option value="'. $row['id'] . '"';
                                    if($row['id'] == $_POST['player_1'])
                                    {
                                        echo ' selected ';
                                    }
                                    echo '>' . $row['nombre_jugador'] . '</option>';
                            }
                            ?>
                        </select>
                        <label>Player 2</label>
                        <select class="browser-default" name="player_2">
                            <option selected="true" disabled="disabled">Elija un Jugador..</option>    
                            <?php
                            foreach ($jugadores as $row) {
                                echo '<option value="'. $row['id'] . '"';
                                    if($row['id'] == $_POST['player_2'])
                                    {
                                        echo ' selected ';
                                    }
                                    echo '>' . $row['nombre_jugador'] . '</option>';
                            }
                            ?>
                        </select>
                            <div class="row">
                                <div class="col s12">
                                    <div class="input-field col s12">
                                        <button class="btn waves-effect waves-light" type="submit" name="action" id="enviar">Comparar
                                            <i class="material-icons left">done</i>
                                        </button>
                                            <?php         
                                                if(!isset($_POST['player_1']) || !isset($_POST['player_2'])){                               
                                                            echo '</br></br>Elija dos jugadores para comparar..';
                                                }
                                            ?>
                                    </div>
                                </div>
                            </div>
                     </div>
            <div class="row">
                <div class="col s12">
                    <div class="input-field col s12">
                               <?php         
                               if(isset($_POST['player_1']) && isset($_POST['player_2'])){                               
                                            echo '<h5 style=" text-align: center;">"Player vs. Player!" (partidos en distintos equipos)</h5>';
                                            echo '<canvas id="compara" style="max-height: 600px"></canvas>';
                                            echo '</br></br>';
                                            echo '<h5 style=" text-align: center;">"Friendship" (partidos en el mismo equipo)</h5>';
                                            echo '<canvas id="friend" style="max-height: 600px"></canvas>';
                                    }
                                ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
  </div>
<!--  Scripts-->
<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<!-- Compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script> 
<!--chartjs-->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
<!-- Datatables -->
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<!-- Sidenav -->
<script>
    (function($){
        $(function(){
        $('.sidenav').sidenav();
        }); 
    })(jQuery); 
</script>
<!-- datatables -->
<script>
    $(document).ready(function() {
        $('#tabla').DataTable( {
            "paging":   false,
            "searching": false,
            "info":     false,
            "order": [[ 2, 'desc' ], [ 5, 'desc' ], [ 3, 'asc' ], [ 1, 'desc' ], [ 0, 'asc' ]]
        } );
        
    } );
</script>
<!-- Tabs -->
<script>
    $(document).ready(function(){
    $('.tabs').tabs();
}); 
</script>
<!-- charts -->
<script>
        window.chartColors = {
        red: 'rgb(255, 99, 132)',
        orange: 'rgb(255, 159, 64)',
        yellow: 'rgb(255, 205, 86)',
        green: 'rgb(75, 192, 192)',
        blue: 'rgb(54, 162, 235)',
        purple: 'rgb(153, 102, 255)',
        grey: 'rgb(201, 203, 207)'
    };
    var color = Chart.helpers.color;
    var horizontalBarChartData = {
        labels: ['Partidos Ganados', 'Partidos Perdidos', 'Partidos Empatados'],
        datasets: [{
            label: '<?php 
            
            
            if(isset($_POST['player_1']) && isset($_POST['player_2'])){
                
                foreach ($jugadores as $row) {                  
                    if($row['id'] == $_POST['player_1'])
                    {
                        echo $row['nombre_jugador'];
                    }
                }
            }
            ?>',
            backgroundColor: color(window.chartColors.green).alpha(0.5).rgbString(),
            borderColor: window.chartColors.green,
            borderWidth: 1,
            data: [
                            <?php         
                            if(isset($_POST['player_1']) && isset($_POST['player_2'])){                               
                                foreach ($compara as $row) {
                                        echo $row['p1_PG'] . ',';
                                        echo $row['p1_PP'] . ',' ;
                                        echo $row['p1_PE'];
                                        
                                    }
                                }
                            ?>
            ]
        }, {
            label: '<?php 
            if(isset($_POST['player_1']) && isset($_POST['player_2'])){
                
                foreach ($jugadores as $row) {                  
                    if($row['id'] == $_POST['player_2'])
                    {
                        echo $row['nombre_jugador'];
                    }
                }
            }
            ?>',
            backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
            borderColor: window.chartColors.blue,
            data: [
                            <?php         
                            if(isset($_POST['player_1']) && isset($_POST['player_2'])){                               
                                foreach ($compara as $row) {
                                        echo $row['p2_PG'] . ',';
                                        echo $row['p2_PP'] . ',' ;
                                        echo $row['p2_PE'];
                                        
                                    }
                                }
                            ?>
            ]
        }]

    };
    var horizontalBarChartDataFriend = {
        labels: ['Partidos Ganados', 'Partidos Perdidos', 'Partidos Empatados'],
        datasets: [{
            label: '<?php 
            if(isset($_POST['player_1']) && isset($_POST['player_2'])){
                    foreach ($jugadores as $row) {                  
                        if($row['id'] == $_POST['player_1'])
                        {
                            echo $row['nombre_jugador'];
                        }
                    }
                    echo ' - ';
                    foreach ($jugadores as $row) {                  
                        if($row['id'] == $_POST['player_2'])
                        {
                            echo $row['nombre_jugador'];
                        }
                    }
                }
            ?>',
            backgroundColor: color(window.chartColors.orange).alpha(0.5).rgbString(),
            borderColor: window.chartColors.orange,
            borderWidth: 1,
            data: [
                            <?php         
                            if(isset($_POST['player_1']) && isset($_POST['player_2'])){                               
                                foreach ($friend as $row) {
                                        echo $row['PG'] . ',';
                                        echo $row['PP'] . ',' ;
                                        echo $row['PE'];
                                        
                                    }
                                }
                            ?>
            ]
        }]
    };
    window.onload = function() {
    var ctx = document.getElementById('compara').getContext('2d');
        window.myHorizontalBar = new Chart(ctx, {
            type: 'horizontalBar',
            data: horizontalBarChartData,
            options: {
                //'false' para que se vea bien en mobile (y también se agrega al canvas style="max-height: 600px")
                maintainAspectRatio : false,
                scales: {
                    yAxes: [{
                            scaleLabel: {
                                display: true
                            }/*,
                            gridLines: {
                                drawBorder: false,
                                color: [color(window.chartColors.orange).alpha(0.5).rgbString(), color(window.chartColors.orange).alpha(0.5).rgbString(), color(window.chartColors.orange).alpha(0.5).rgbString()]
                            }*/
                        }],
                    xAxes: [{
                    ticks: {
                        stepSize: 1,
                    },
                    scaleLabel: {
                        display: true
                    },
                    }]
                },
                elements: {
                    rectangle: {
                        borderWidth: 2,
                    }
                },
                responsive: true,
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Total Partidos enfrentados: <?php         
                            if(isset($_POST['player_1']) && isset($_POST['player_2'])){                               
                                foreach ($compara as $row) {
                                        echo $row['PJ'] . "'";
                                    }
                                }
                            ?>
                }
            }
        });
    var ctxFriend = document.getElementById('friend').getContext('2d');
        window.myHorizontalBar = new Chart(ctxFriend, {
            type: 'horizontalBar',
            data: horizontalBarChartDataFriend,
            options: {
                //'false' para que se vea bien en mobile (y también se agrega al canvas style="max-height: 600px")
                maintainAspectRatio : false,
                scales: {
                    yAxes: [{
                            scaleLabel: {
                                display: true
                            }/*,
                            gridLines: {
                                drawBorder: false,
                                color: [color(window.chartColors.orange).alpha(0.5).rgbString(), color(window.chartColors.orange).alpha(0.5).rgbString(), color(window.chartColors.orange).alpha(0.5).rgbString()]
                            }*/
                        }],
                    xAxes: [{
                    ticks: {
                        stepSize: 1,
                        //min: 0,
                        //max: 4
                    },
                    scaleLabel: {
                        display: true
                    },
                    }]
                },
                elements: {
                    rectangle: {
                        borderWidth: 2,
                    }
                },
                responsive: true,
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Total Partidos en el mismo equipo: <?php         
                            if(isset($_POST['player_1']) && isset($_POST['player_2'])){                               
                                foreach ($friend as $row) {
                                        echo $row['PJ'] . "'";
                                    }
                                }
                            ?>
                }
            }
        });
    };
</script>
</body>
</html>

