<?php
/*Valida usuario y password en la BD y guarda el usuario en "session"*/
include "config.php";
if(isset($_POST['submit'])){
    $uname = mysqli_real_escape_string($con,$_POST['user']);
    $password = mysqli_real_escape_string($con,$_POST['password']);
    if ($uname != "" && $password != ""){
        $sql_query = "select count(*) as cntUser from usuarios where usuario='".$uname."' and password='".$password."'";
        $result = mysqli_query($con,$sql_query);
        $row = mysqli_fetch_array($result);
        $count = $row['cntUser'];
        if($count > 0){
            $_SESSION['uname'] = $uname;
            header('Location: index.php');
        }
        else{
            echo "Usario y/o clave incorrecta";
        }
    }
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
		<li><a href="jugadores.php">Jugadores</a></li>
        <?php         
            if(!isset($_SESSION['uname'])){                               
                echo '<li class="active"><a href="login.php">Log In</a></li>';
            }
            else{
                echo '<li><a href="logout.php">' . $_SESSION['uname'] . ' (Logout)</a></li>';
            }
        ?>
      </ul>
      <ul id="nav-mobile" class="sidenav">
		<li><a href="index.php">Resultados</a></li>
		<li><a href="fechas.php">Fechas</a></li>
		<li><a href="jugadores.php">Jugadores</a></li>
        <?php         
            if(!isset($_SESSION['uname'])){                               
                echo '<li class="active"><a href="login.php">Log In</a></li>';
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
            <form class="col s6" method="post" action="">
                <div class="row">
                    <div class="input-field col s12">
                        <i class="material-icons prefix">account_circle</i>
                        <input id="icon_prefix" type="text" class="validate" name="user">
                        <label for="icon_prefix">Usuario</label>
                    </div>
                    <div class="input-field col s12">
                        <i class="material-icons prefix">vpn_key</i>
                        <input id="password"  type="password" class="validate" name="password">
                        <label for="password">Clave</label>
                    </div>
                    <div class="input-field col s12" style="margin-left:44px">           
                        <button class="btn waves-effect waves-light" type="submit" name="submit" id="enviar">Enviar
                            <i class="material-icons right">send</i>
                        </button>
                    </div>
                </div>
            </form>
        </div>      
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
</body>
</html>
