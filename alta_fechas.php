
<?php
include "db.php";
session_start();
    if(!isset($_SESSION['uname'])){
        header('Location: index.php');
        die();
    }
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $data = $conn->query("SELECT id, nombre_jugador
        from jugadores 
        order by nombre_jugador asc")->fetchAll();
        if (!empty($_POST)) {
                $sql = "INSERT INTO resultados (fecha_hora, id_jugador, id_cancha, resultado, equipo) VALUES (?,?,?,?,?)";
                foreach ($_POST["equipo_1"] as $row)
                {
                    $anio = substr($_POST["fecha_partido"], 6, 4);
                    $mes = substr($_POST["fecha_partido"], 3, 2);
                    $dia = substr($_POST["fecha_partido"], 0, 2);
                    $hora = substr($_POST["hora_partido"], 0, 5);
                    $fecha_hora = $anio . '-' . $mes . '-' . $dia . ' ' . $hora;
                    $id_cancha = $_POST["id_cancha"];               
                    $id_jugador = $row;
                    $equipo = 1;
                    if ($_POST["resultado"] == '1')
                    {
                        $resultado = 1;
                    }
                    if ($_POST["resultado"] == '2')
                    {
                        $resultado = 0;
                    }
                    if ($_POST["resultado"] == '3')
                    {
                        $resultado = 2;
                    }
                    $conn->prepare($sql)->execute([$fecha_hora, $id_jugador, $id_cancha, $resultado, $equipo]);
                }
                foreach ($_POST["equipo_2"] as $row)
                {
                    $anio = substr($_POST["fecha_partido"], 6, 4);
                    $mes = substr($_POST["fecha_partido"], 3, 2);
                    $dia = substr($_POST["fecha_partido"], 0, 2);
                    $hora = substr($_POST["hora_partido"], 0, 5);
                    $fecha_hora = $anio . '-' . $mes . '-' . $dia . ' ' . $hora;
                    $id_cancha = $_POST["id_cancha"];
                    $id_jugador = $row;
                    $equipo = 2;
                    if ($_POST["resultado"] == '1')
                    {
                        $resultado = 0;
                    }
                    if ($_POST["resultado"] == '2')
                    {
                        $resultado = 1;
                    }
                    if ($_POST["resultado"] == '3')
                    {
                        $resultado = 2;
                    }
                    $conn->prepare($sql)->execute([$fecha_hora, $id_jugador, $id_cancha, $resultado, $equipo]);
                }
                header("Location: fechas.php");
                die();
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
            <form action="alta_fechas.php" method="post">
                <div class="row">
                <div class="col s12">
                    <div class="input-field col s12">
                        <i class="material-icons prefix">date_range</i>
                            <input type="text" class="datepicker" name="fecha_partido">
                            <label for="datepicker">Fecha Partido</label>
                    </div>       
                </div>
                </div>        
                <div class="row">
                <div class="col s12">
                    <div class="input-field col s12">
                        <i class="material-icons prefix">access_time</i>
                            <input type="text" class="timepicker" id="timepicker" name="hora_partido">
                            <label for="timepicker">Hora Partido</label>
                    </div>       
                </div>
                </div>
                <div class="row">
                <div class="col s12">
                    <div class="input-field col s12">
                        <i class="material-icons prefix">my_location</i>
                            <select id="id_cancha" name="id_cancha">
                            <option value="1">La Estancia</option>
                            <option value="2">El Rose</option>
                            </select>
                            <label>Cancha</label>
                        </div>
                    </div>       
                </div>
                <div class="row">
                    <div class="col s12">
                    <div class="input-field col s12">
                    <i class="material-icons prefix">group</i>
                        <select multiple id="equipo_1" name="equipo_1[]">
                            <?php
                            foreach ($data as $row) {
                                echo '<option value="' . $row['id'] . '">'. $row['nombre_jugador'] . '</option>';
                            }
                            ?>
                        </select>
                        <label>Equipo 1</label>
                    </div>
                    </div>       
                </div>
                <div class="row">
                    <div class="col s12">
                    <div class="input-field col s12">
                    <i class="material-icons prefix">group</i>
                        <select multiple id="equipo_2" name="equipo_2[]">
                            <?php
                            foreach ($data as $row) {
                                echo '<option value="' . $row['id'] . '">'. $row['nombre_jugador'] . '</option>';
                            }
                            ?>
                        </select>
                        <label>Equipo 2</label>
                    </div>
                    </div>       
                </div>
                <div class="row">
                <div class="col s12">
                    <div class="input-field col s12">
                        <i class="material-icons prefix">emoji_events</i>
                            <select id="resultado" name="resultado">
                            <option value="1">Ganó Equipo 1</option>
                            <option value="2">Ganó Equipo 2</option>
                            <option value="3">Empate</option>
                            </select>
                            <label>Resultado</label>
                        </div>
                    </div>       
                </div>
                <div class="row">
                <div class="col s12">
                    <div class="input-field col s12" style="margin-left:44px">           
                        <button class="btn waves-effect waves-light" type="submit" name="action" id="enviar">Enviar
                                <i class="material-icons right">send</i>
                            </button>
                        </div>
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
<!-- Timepicker -->
<script>
    const defaultTime = '20:00';
    const timepicker = document.getElementById('timepicker');
    const timeInstance = M.Timepicker.init(timepicker, {
        defaultTime: defaultTime
    });
    timeInstance._updateTimeFromInput();
    timeInstance.done();
    $('#equipo_3').focus().select()
</script>
<!-- Datepicker -->
<script>
function getMartes(d) {
    var d = new Date(d);
    var day = d.getDay(),
        diff = d.getDate() - day + (day == 0 ? -5:2); // 2 = martes
    martes=new Date(d.setDate(diff));
    var curr_date = martes.getDate();
    var curr_month = martes.getMonth();
    var curr_year = martes.getFullYear();
    return new Date(curr_year,curr_month,curr_date);
}
var d = getMartes(new Date());
$('.datepicker').datepicker({ 
        format: 'dd/mm/yyyy',
        setDefaultDate: true,
        defaultDate: d,
        i18n: {
            months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Set", "Oct", "Nov", "Dic"],
            weekdays: ["Domingo","Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
            weekdaysShort: ["Dom","Lun", "Mar", "Mie", "Jue", "Vie", "Sab"],
            weekdaysAbbrev: ["D","L", "M", "M", "J", "V", "S"]
        }
    });
</script>
<!-- Select -->
<script>
    $(document).ready(function(){
        $('select').formSelect();
    });
</script>
</body>
</html>
