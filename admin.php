<?php 
error_reporting(0);
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

if ($logged == 0) {
    header("Location: login.php");
    die();
}

$login = $_COOKIE["login"];
$json_data = file_get_contents('data/logins.json');
$json_data = Decrypted($json_data);
$decoded = json_decode($json_data, true);
$userdata = $decoded[$login];

if ($userdata["admin"] == false) {
    header("Location: login.php");
    die();
}

if($_POST["user"] !== "" && $_POST["user"] !== null) {
    $user = $_POST["user"];
    $json_data = file_get_contents('data/logins.json');
    $json_data = Decrypted($json_data);
    $decoded = json_decode($json_data, true);

    if ($_POST["admin"] == "true") {
        $decoded[$user]["admin"] = true;
    } else {
        $decoded[$user]["admin"] = false;
    }

    if ($_POST["org"] == "true") {
        $decoded[$user]["organizer"] = true;
    } else {
        $decoded[$user]["organizer"] = false;
    }

    $json_data = json_encode($decoded);
    $json_data = Encrypted($json_data);
    file_put_contents('data/logins.json', $json_data);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Admin panel (nebezpečná zóna)</h1>

    <?php 
    
    if($_GET["user"] !== "" && $_GET["user"] !== null) {
        $user = $_GET["user"];
        $json_data = file_get_contents('data/logins.json');
        $json_data = Decrypted($json_data);
        $decoded = json_decode($json_data, true);
        if ($decoded[$user]["admin"] == true) {$isAdmin = "checked";} else {$isAdmin = "";}
        if ($decoded[$user]["organizer"] == true) {$isOrg = "checked";} else {$isOrg = "";}

        echo <<<END
            <form action="admin.php" method="post">
                <input type="text" name="user" value="$user"> <br>
                <input type="checkbox" name="admin" value="true" $isAdmin> Admin<br>
                <input type="checkbox" name="org" value="true" $isOrg> Organizátor<br>
                <input type="submit" value="Uložit">
            </form>
        END;

    } else {
        echo <<<END
            <form action="admin.php" method="get">
                <input type="text" name="user" placeholder="Uživatel">
                <input type="submit" value="Upravit">
            </form>
        END;
    }

    ?>

</body>
</html>