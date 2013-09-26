<?php

require_once 'config.php';

header('Content-type: application/json');

$response = array();

if(!isset($_GET['ticket']))
{
    $response['sucess'] = 0;
    echo json_encode($response);
    exit();
}

$ticket = $_GET['ticket'];

$dblink = mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_passwd'], $config['mysql_database']) or die ("Impossible de se connecter à la base de donnée :-(");

$ticket = mysqli_escape_string($dblink, $ticket);
$result = mysqli_query($dblink, "SELECT user FROM `auth_user` INNER JOIN auth_ticket ON id = userid WHERE ticket=\"$ticket\"");
mysqli_query($dblink, "DELETE FROM auth_ticket WHERE ticket=\"$tichet\"");

if(mysqli_num_rows($result) == 1)
{
    $row = mysqli_fetch_assoc($result);
    $response['sucess'] = 1;
    $response['username'] = $row['user'];
    echo json_encode($response);
}
else
{
    $response['sucess'] = 0;
    echo json_encode($response);
}

?>
