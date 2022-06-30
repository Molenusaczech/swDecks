<?php 
error_reporting(0);

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
    $decoded = json_decode($json_data, true);
    $limit = $_POST["limit"];

    if(!isset($decoded[$tournamentId])) {

    $decoded[$tournamentId] = array("id" => $tournamentId, "limit" => $limit, "organizer" => $login, "players" => array());
    $finalJson = json_encode($decoded);
    $myfile = fopen("data/tournaments.json", "w") or die("Unable to open file!");
    fwrite($myfile, $finalJson);
    fclose($myfile);
    echo "<p style='background-color: green'> Turnaj s id $tournamentId byl vytvořen</p>";
    } else {
        echo "<p style='background-color: red'> Turnaj s id $tournamentId již existuje! Vyberte prosím jiné ID</p>";
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
    <h1>Organizátorský panel</h1>

    <h2> Vytvořit turnaj </h2>
    <form action="orgpanel.php" method="post">
        ID turnaje: <input name="tournamentid" type="text" placeholder="ID Turnaje"> <br>
        Omezení: 
        <select name="limit" class="select">
            <!--<option value="none">Bez Omezení</option> -->

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
        <input type="submit" value="Založit turnaj">

        <h2>Tvoje turnaje</h2>

    </form>

    <table> 
        <tr> 
            <th>ID Turnaje</th>
            <th>Odkaz</th>
            <th>Počet Hráčů</th>
        </tr>


    <?php 
        
        $json_data = file_get_contents('data/tournaments.json');
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
                    <td><a href=$orgLink>Zobrazit balíčky</a></td>
                </tr>

                END;
            }
        }

        ?>


    </table>

</body>
</html>