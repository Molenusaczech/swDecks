<?php 

$key = "testsifry";

function Encrypted($text) {
 
    $string = $text;
    $pass = $key;
    $method = 'aes128';
    return openssl_encrypt($string, $method, $pass);

}

function Decrypted($text) {
    $string = $text;
    $pass = $key;
    $method = 'aes128';
    return openssl_decrypt($string, $method, $pass);
}

error_reporting(0);
if ($_COOKIE["login"] !== "" && $_COOKIE["login"] !== null && $_COOKIE["token"] !== "" && $_COOKIE["token"] !== null) {
    $login = $_COOKIE["login"];
    $json_data = file_get_contents('data/logins.json');
    $json_data = Decrypted($json_data);
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
        $username = $login;
    } else {$logged = 0;}
} else {$logged = 0;}

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
    


        <?php if ($logged == 1) { 
            echo "<span class='logged'>Přihlášen jako: $username</span>";
     
        } else {
            echo "<span class='logged'>Nejsi přihlášen</span>";
        } 

        $login = $_COOKIE["login"];
        $json_data = file_get_contents('data/logins.json');
        $json_data = Decrypted($json_data);
        $decoded = json_decode($json_data, true);
        $userdata = $decoded[$login];

        if ($userdata["organizer"] == true)    {
            echo <<<END
            <a href="orgpanel.php" class="menu">Organizátorský panel</a>
            END;
        } else {
            echo <<<END
            <span class="menu">Nemáš přístup k organizátorském panelu</span>
            END;
        }

        if ($userdata["admin"] == true)    {
            echo <<<END
            <a href="admin.php" class="menu">Admin panel</a>
            END;
        } else {
            echo <<<END
            <span class="menu">Nemáš přístup k admin panelu</span>
            END;
        }

         if ($logged == 1) { 
            echo <<<END
            <a href="logout.php" class="menu">Odhlásit</a>
            END;
        } else {
            echo <<<END
            <a href="login.php" class="menu">Přihlásit</a>
            END;
        } ?>

    
</body>
</html>