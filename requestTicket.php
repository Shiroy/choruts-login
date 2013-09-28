<?php

require_once 'config.php';

if(!isset($_GET['appli']))
    exit();

if(!isset($_COOKIE['choruts_auth']))
{    
    header("Location: ".$_GET['appli']);
    setcookie("st", 0, 0, "/");
    
    exit();
}
    
$session = $_COOKIE['choruts_auth'];

$dblink = mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_passwd'], $config['mysql_database']) or die ("Impossible de se connecter à la base de donnée :-(");

$session = htmlspecialchars($session);
$session = mysqli_escape_string($dblink, $session);
$ip = $_SERVER['REMOTE_ADDR'];

$session = "SELECT user_id FROM auth_session WHERE sessionkey=\"$session\" AND ip=\"$ip\" AND (expire) > UNIX_TIMESTAMP()";
$result = mysqli_query($dblink, $session);
//echo $session;

if(mysqli_num_rows($result) == 1)
{
    $ticket = uniqid("choruts-ticket");
    $sessionArray = mysqli_fetch_assoc($result);
    $user_id = $sessionArray['user_id'];
    mysqli_query($dblink, "INSERT INTO auth_ticket VALUES (\"$ticket\", $user_id)");
    setcookie("st", $ticket, 0, "/");
    
    header("Location: ".$_GET['appli']);
    exit();  
}

setcookie("st", 0, 0, "/");
header("Location: ".$_GET['appli']);

?>
