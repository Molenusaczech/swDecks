<?php 
error_reporting(0);

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if ($_POST["tid"] !== "" && $_POST["tid"] !== null) {
    $redirect = "player.php?tid=".$_POST["tid"];

} else {
    $redirect = "index.php";
}

if ($_COOKIE["login"] !== "" && $_COOKIE["login"] !== null && $_COOKIE["token"] !== "" && $_COOKIE["token"] !== null) {
    $login = $_COOKIE["login"];
    $json_data = file_get_contents('data/logins.json');
    $decoded = json_decode($json_data, true);
    //echo "debug1";


    $userdata = $decoded[$login];
    $token = $userdata["token"];

    $cookieToken = $_COOKIE["token"];
    //echo "debug2 <br>";
    //echo "token: $token <br>";
    //echo "cookie: $cookieToken <br>";

    if ($token == $_COOKIE["token"]) {
        //echo "debug3";
        $logged = 1;
        header("Location: $redirect");
        die();
    }
}

error_reporting(0);
$authed = 3;
if($_POST["login"] !== "" && $_POST["password"] !== "" && $_POST["login"] !== null && $_POST["password"] !== null && $authed !== 1) {
    
    $login = $_POST["login"];
    $password = $_POST["password"];

    $url = "https://scratchwars.cloud/public/api/auth";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
    $headers = array(
       "Content-Type: application/x-www-form-urlencoded",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    
    $data = "uid=$login&pin=$password";
    
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    
    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    
    $resp = curl_exec($curl);
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    //var_dump($resp);
    
    //echo "<br>code:";
    //echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
    //$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if($code == 200) {
        $authed = 1;
        
    } else {
        $authed = 0;
    }

}

if ($authed == 1) {
    $json_data = file_get_contents('data/logins.json');
    $decoded = json_decode($json_data, true);
    
    
    if (isset($decoded[$login])) {
        $userdata = $decoded[$login];
        $token = $userdata["token"];
        setcookie("token", $token);
        setcookie("login", $login);
        $logged = 1;
        header("Location: $redirect");
        die();
    } else {
        $decoded[$login] = array("token" => generateRandomString(32), "organizer" => false, "admin" => false);
        $finalJson = json_encode($decoded);
        $myfile = fopen("data/logins.json", "w") or die("Unable to open file!");
        fwrite($myfile, $finalJson);
        fclose($myfile);

        $userdata = $decoded[$login];
        $token = $userdata["token"];
        setcookie("token", $token);
        setcookie("login", $login);
        $logged = 1;
        header("Location: $redirect");
        die();
    }

}

if ($logged == 1) {
    header("Location: $redirect");
    die();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Document</title>

</head>
<body>
    <div class="authWindow"> 
        <!--<p>Login:</p> -->
        <form action="login.php" method="post">
            <input type="text" name="login" placeholder="App ID"> <br>
            <input type="password" name="password" placeholder="PIN"> <br>

            <?php 
            
            if ($_GET["tid"] !== "" && $_GET["tid"] !== null) {
                $tid = $_GET["tid"];
                echo <<<END
            <input type="hidden" name="tid" value=$tid>
            END;
            }
            
            ?>

            <input type="submit" value="Přihlásit se">
        </form>
    
        <?php 
        
        if ($authed == 0) {
            echo <<<END
            <span class="error">Špatné ID nebo PIN</span>
            END;
        } elseif ($authed == 1) {
            echo <<<END
            <span class="success">Přihlášení proběhlo úspěšně</span>
            END;
        }

        ?>

        </p>
    </div>
</body>
</html>