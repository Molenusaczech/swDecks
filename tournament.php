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

$json_data = file_get_contents('data/tournaments.json');
$json_data = Decrypted($json_data);
$decoded = json_decode($json_data, true);
$tid = $_GET["tid"];

$tournamentId = $_GET["tid"];

$tournamentData = $decoded[$tid];
$org = $tournamentData["organizer"];

if($org !== $_COOKIE["login"]) {
    header("Location: orgpanel.php");
    die();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>turnaj - <?php echo $tid;?></title>

    <style>
table, th, td {
  border:1px solid black;
}
</style>

</head>
<body>

    <a href="orgpanel.php">Zpět na organizátorský panel</a> <br>
    
    <?php 

    echo <<<END
        Turnaj: $tournamentId <br> Organizátor: $org
    END;

    ?>

    <table>

        <tr> 
            <th>ID Hráče</th>
            <th>Hrdina</th>
            <th>Zbraně</th>
        </tr>

        <?php 
        
        $players = $tournamentData["players"];

        foreach ($players as $index1) {
            echo "<tr>";
            for ($x = 0; $x <= 6; $x++) {
                if ($index1[$x] !== "") {
                    echo "<td>$index1[$x]</td>"; 
                }
            }
            echo "</tr>";
        }

        ?>

    </table>

</body>
</html>