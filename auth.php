<?php

require_once 'config.php';

if(!isset($_GET['appli']))
    exit();

if(isset($_POST['choruts_login']) && isset($_POST['choruts_password']))
{
    $username = $_POST['choruts_login'];
    $username = htmlspecialchars($username);    
    
    $dblink = mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_passwd'], $config['mysql_database']) or die ("Impossible de se connecter à la base de donnée :-("); 
    $username = mysqli_escape_string($dblink, $username);   
    
    $pass_key = strtoupper(sha1(strtoupper($_POST['choruts_login']).":".$_POST['choruts_password']));
    
    $query = "SELECT id FROM auth_user WHERE user=\"".$username."\" AND password=\"$pass_key\"";
    //echo $query;      
    
    if($login = mysqli_query($dblink, $query) and mysqli_num_rows($login) > 0) //Pas de résultat
    {
        $row = mysqli_fetch_assoc($login);
        $user_id = $row['id'];
        $sessionkey = uniqid("", true);
        setcookie("choruts_auth", $sessionkey, time() + 2 * 3600, "/", false, true);
        $ip = $_SERVER['REMOTE_ADDR']; //Evite le vol de sessionkey
        $expire = time() + 2* 3600;
        
        mysqli_query($dblink, "INSERT INTO auth_session(sessionkey, ip, user_id, expire) VALUES (\"$sessionkey\", \"$ip\", $user_id, $expire)");        
    }
    else
    {
        $error_msg = "<p>Les informations transmises ne permettent pas de vous authentifier</p><br/>";
    }  
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Connexion au portail choruts</title>
    </head>
    
    <body>
        <h1>Service de connexion centrale</h1>
        
        <form method="post" action="auth.php?appli=<?php echo $_GET['appli'] ?>">
        
            <?php
            if(isset($error_msg))
                echo $error_msg;
            ?>
                        
            Nom d'utilisateur<br/>
            <input type="text" name="choruts_login"/><br/>
            Mot de passe<br/>
            <input type="text" name="choruts_password"/><br/>
            <input type="submit" value="Connexion"/><br/>
        </form>
    </body>
</html>
