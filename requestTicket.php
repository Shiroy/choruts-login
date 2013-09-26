<?php

require_once 'config.php';

if(!isset($_GET['session']) or !isset($_GET['appli']))
    exit();
    
$session = $_GET['session'];

$dblink = mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_passwd'], $config['mysql_database']) or die ("Impossible de se connecter à la base de donnée :-(");

$session = htmlspecialchars($session);
$session = mysqli_escape_string($dblink, $session);
$ip = $_SERVER['REMOTE_ADDR'];

$session = "SELECT user_id FROM auth_session WHERE sessionkey=\"$session\" AND ip=\"$ip\" AND (expire + 2*3600) > UNIX_TIMESTAMP()";
$result = mysqli_query($dblink, $session);
//echo $session;

if(mysqli_num_rows($result) == 1)
{
    $ticket = uniqid("choruts-ticket");
    $sessionArray = mysqli_fetch_assoc($result);
    $user_id = $sessionArray['user_id'];
    mysqli_query($dblink, "INSERT INTO auth_ticket VALUES (\"$ticket\", $user_id)");
    setcookie("choruts_ticket", $ticket, time() + 60, null, null, false, true);
    
    header("Location: ".$_GET['appli']);  
    exit();  
}

header("Location: auth.php?appli=".$_GET['appli']);  

?>
