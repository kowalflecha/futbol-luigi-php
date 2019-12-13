<?php
    /*Borra "session" y redirige a pantalla de login*/
    session_start();
    session_destroy() ;
    header('Location: login.php');
    die();
?>