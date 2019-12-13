<?php
include "db.php";
session_start();
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully";
        $data = $conn->query("SELECT nombre_jugador, posicion, _timestamp
        from jugadores 
        order by nombre_jugador asc;")->fetchAll();
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
		<li><a href="fechas.php">Fechas</a></li>
		<li class="active"><a href="jugadores.php">Jugadores</a></li>
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
		<li><a href="fechas.php">Fechas</a></li>
		<li class="active"><a href="jugadores.php">Jugadores</a></li>
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
			  <div class="input-field col s12">
                <?php         
                    if(!isset($_SESSION['uname'])){                               
                        echo '<a class="waves-effect waves-light btn disabled" href="alta_jugadores.php"><i class="material-icons left">add</i>Cargar Jugador</a>';
                    }
                    else{
                        echo '<a class="waves-effect waves-light btn " href="alta_jugadores.php"><i class="material-icons left">add</i>Cargar Jugador</a>';
                    }
                ?>
			  </div>
		  </div>
		</div>

            <table class="highlight">
                <thead>
                <tr>
                    <th>Jugador</th>
                    <th>Posición</th>
                </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($data as $row) {
                            echo '<tr>';
                            echo "<td>" . $row['nombre_jugador']. "</td>";
                            echo "<td>" . $row['posicion']. "</td>";
                            echo '</tr>';
                        }
                    ?>
                </tbody>
            </table>
    </div>
  </div>

 
<!--  Scripts-->
<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<!-- Compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script> 
<!-- Sidenav -->
<script>
    (function($){
        $(function(){
        $('.sidenav').sidenav();
        }); 
    })(jQuery); 
</script>
<!-- Select -->
<script>
    $(document).ready(function(){
        $('select').formSelect();
    });
</script>
</body>
</html>
