<?php
include "db.php";
session_start();
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully";
        $data = $conn->query("
                            select resultados.fecha_hora 
                            FROM resultados
                            group by resultados.fecha_hora order by resultados.fecha_hora ASC
                            ;")->fetchAll(); 
                        
            if (!empty($_POST)) {
                $fecha = $conn->prepare("select resultados.fecha_hora, canchas.nombre_cancha, jugadores.nombre_jugador, 
                            (CASE WHEN resultados.equipo = 1 THEN 'Equipo 1' WHEN resultados.equipo = 2 THEN 'Equipo 2' END) as equipo, 
                            (CASE WHEN resultados.resultado = 1 THEN 'G' WHEN resultados.resultado = 0 THEN 'P' WHEN resultados.resultado = 2 THEN 'E' END) as resultado 
                            FROM resultados, jugadores, canchas WHERE resultados.id_jugador = jugadores.id 
                            and resultados.id_cancha = canchas.id 
                            and resultados.fecha_hora = ? 
                            order by resultados.fecha_hora");   
                            $fecha_id = $_POST["fecha"];
                            $fecha->execute([$fecha_id]); 
                            $fecha_resultado = $fecha->fetchAll();
                    } 
            else {  
            // echo "Salí de ahí maravilla";
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
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
	<title>Fútbol Luigi - Resultados y estadísticas</title>
	<!-- Compiled and minified CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
	<!-- Icons -->
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
<nav class="blue darken-4" role="navigation">
    <div class="nav-wrapper container"><a id="logo-container" href="index.php" class="brand-logo">Fútbol Luigi</a>
      <ul class="right hide-on-med-and-down">
		<li><a href="index.php">Resultados</a></li>
		<li class="active"><a href="fechas.php">Fechas</a></li>
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
		<li><a href="index.php">Resultados</a></li>
		<li class="active"><a href="fechas.php">Fechas</a></li>
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
    <form action="fechas.php" method="post">
     <div class="row">
		    <div class="col s12">
               <div class="input-field col s12">
                
                <div class="form-group col s12 m6">
                <label>Fecha</label>
                <select class="browser-default" id="fecha" name="fecha">
                    <?php
                    foreach ($data as $row) {
                        echo '<option value="' . $row['fecha_hora'] . '"';
                        if($row['fecha_hora'] == $_POST['fecha'])
                        {
                            echo ' selected ';
                        }
                        echo '>' . date_format(date_create($row['fecha_hora']),"d/m/Y") . '</option>';
                    }
                    ?>
                </select>
                </div>
              </div>
            </div>       
	    </div>
		<div class="row">
		  <div class="col s12">
			  <div class="input-field col s12">
                    <button class="btn waves-effect waves-light" type="submit" name="action" id="enviar">Ver Fecha
                        <i class="material-icons left">done</i>
                    </button>
                    <?php         
                        if(!isset($_SESSION['uname'])){                               
                            echo '<a class="waves-effect waves-light btn disabled" href="alta_fechas.php"><i class="material-icons left">add</i>Cargar Fecha</a>';
                        }
                        else{
                            echo '<a class="waves-effect waves-light btn" href="alta_fechas.php"><i class="material-icons left">add</i>Cargar Fecha</a>';
                        }
                    ?>
			  </div>
		  </div>
		</div>
    </form>
    
    <div class="row">
	  <div class="col s10"  >
        <?php if (!empty($_POST)) {
            
            echo '<table class="highlight" ">
                <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Cancha</th>
                    <th>Jugador</th>
                    <th>Equipo</th>
                    <th>Resultado</th>
                </tr>
                </thead>
                <tbody>';
                    foreach ($fecha_resultado as $fecha_row) {
                        echo '<tr>';
                        echo "<td>" . date_format(date_create($fecha_row['fecha_hora']),"d/m/Y") . "</td>";
                        echo "<td>" . $fecha_row['nombre_cancha'] . "</td>";
                        echo "<td>" . $fecha_row['nombre_jugador'] . "</td>";
                        echo "<td>" . $fecha_row['equipo'] . "</td>";
                        echo "<td>" . $fecha_row['resultado'] . "</td>";
                        echo '</tr>';
                    }
                echo '        
                </tbody>
            </table>';
            }?>
		  </div>
		</div>
    </div>
  </div>
<!--  Scripts-->
<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<!-- Compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script> 
<!-- Select -->
    <script>
    $(document).ready(function(){
        $('select').formSelect();
    });
</script>  
<!-- Sidenav -->
<script>
    (function($){
        $(function(){
        $('.sidenav').sidenav();
        }); 
    })(jQuery); 
</script>
</body>
</html>
