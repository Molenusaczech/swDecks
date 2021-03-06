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

function print_var_name($var) {
    foreach($GLOBALS as $var_name => $value) {
        if ($value === $var) {
            return $var_name;
        }
    }

    return false;
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

if ($userdata["organizer"] == false) {
    header("Location: login.php");
    die();
}

if($_POST["tournamentid"] !== "" and $_POST["tournamentid"] !== null) {

    $tournamentId = $_POST["tournamentid"];

    $tournamentId = str_replace(" ", "_", $tournamentId);

    $json_data = file_get_contents('data/tournaments.json');
    $json_data = Decrypted($json_data);
    $decoded = json_decode($json_data, true);
    $limit = $_POST["limit"];

    if(!isset($decoded[$tournamentId])) {

    $decoded[$tournamentId] = array("id" => $tournamentId, "limit" => $limit, "organizer" => $login, "players" => array());
    $finalJson = json_encode($decoded);
    $myfile = fopen("data/tournaments.json", "w") or die("Unable to open file!");
    $finalJson = Encrypted($finalJson);
    fwrite($myfile, $finalJson);
    fclose($myfile);
    echo "<p style='background-color: green'> Turnaj s id $tournamentId byl vytvo??en</p>";
    } else {
        echo "<p style='background-color: red'> Turnaj s id $tournamentId ji?? existuje! Vyberte pros??m jin?? ID</p>";
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
table, th, td {
  border:1px solid black;
}
</style>

</head>
<body>
    <h1>Organiz??torsk?? panel</h1>

    <h2> Vytvo??it turnaj </h2>
    <form action="orgpanel.php" method="post">
        ID turnaje: <input name="tournamentid" type="text" placeholder="ID Turnaje"> <br>
        Omezen??: 
        <select name="limit" class="select">
            <!--<option value="none">Bez Omezen??</option> -->

            <?php 
            
            $json_data = file_get_contents('limits.json');
            $decoded = json_decode($json_data, true);

            foreach($decoded as $limit) {
                //var_dump($limit);
                $filename = $limit["filename"];
                $name = $limit["name"];
                echo "<option value='$filename'>$name</option>";
            }
            
            ?>

        </select> <br>
        <input type="submit" value="Zalo??it turnaj">

        <h2>Tvoje turnaje</h2>

    </form>

    <table> 
        <tr> 
            <th>ID Turnaje</th>
            <th>Odkaz</th>
            <th>Po??et Hr??????</th>
        </tr>


    <?php 
        
        $json_data = file_get_contents('data/tournaments.json');
        $json_data = Decrypted($json_data);

        $decoded = json_decode($json_data, true);

        foreach($decoded as $index) {

            $tournamentId = $index["id"];
            /*$link = "https://".$_SERVER['SERVER_NAME']."/player.php?id=".$tournamentId; */
            $link = "/player.php?tid=".$tournamentId;
            $players = $index["players"];
            $playerCount = sizeof($players);
            //$orgLink = "https://".$_SERVER['SERVER_NAME']."/tournament.php?tid=".$tournamentId;
            $orgLink = "/tournament.php?tid=".$tournamentId;

            if ($index["organizer"] == $_COOKIE["login"]) {
                echo <<<END

                <tr> 
                    <td>$tournamentId</td>
                    <td><a href=$link>$link</a></td>
                    <td>$playerCount</td>
                    <td><a href=$orgLink>Zobrazit bal????ky</a></td>
                </tr>

                END;
            }
        }

        ?>


    </table>

</body>
</html>