<?php 

function apiGet($url) {

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  
  //for debug only!
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  
  $resp = curl_exec($curl);
  curl_close($curl);
  $decoded = json_decode($resp);
  return $decoded;
  
}

error_reporting(0);

$hero = "";
$weapon1 = "";
$weapon2 = "";
$weapon3 = "";
$weapon4 = "";
$weapon5 = "";
$weapon6 = "";

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


// tid set
$tid = $_GET["tid"];

if ($logged == 0) {
    header("Location: login.php?tid=$tid");
    die();
}

//echo "turnaj: $tid";

$json_data = file_get_contents('data/tournaments.json');
$decoded = json_decode($json_data, true);

if (!isset($decoded[$tid])) {
    header("Location: index.php");
    die();
}

if ($_POST["hero"] !== "" and $_POST["hero"] !== null && $_POST["weapon1"] !== "" and $_POST["weapon1"] !== null && $_POST["tid"] !== "" and $_POST["tid"] !== null) {
  //echo "Balíček úspěšně odeslán";
  $hero = $_POST["hero"];
  $weapon1 = $_POST["weapon1"];
  $weapon2 = $_POST["weapon2"];
  $weapon3 = $_POST["weapon3"];
  $weapon4 = $_POST["weapon4"];
  $weapon5 = $_POST["weapon5"];
  $weapon6 = $_POST["weapon6"];
  $tid = $_POST["tid"];
  $user = $_COOKIE["login"];

  $json_data = file_get_contents('data/tournaments.json');
  $decoded = json_decode($json_data, true);
  $tournamentData = $decoded[$_POST["tid"]];
  $limit = $tournamentData["limit"];

  $json_data = file_get_contents('data/tournaments.json');
  $decoded = json_decode($json_data, true);
  $tournamentData = $decoded[$_GET["tid"]];
  $limit = $tournamentData["limit"];

  $heroes = file_get_contents('heroes/'.$limit.'.json');
  $heroes = json_decode($heroes, true);
  array_push($heroes, "");

  $json_data = file_get_contents('data/tournaments.json');
  $decoded = json_decode($json_data, true);
  $tournamentData = $decoded[$_GET["tid"]];
  $limit = $tournamentData["limit"];
  $org = $tournamentData["organizer"];

  $weapons = file_get_contents('weapons/'.$limit.'.json');
  $weapons = json_decode($weapons, true);
  array_push($weapons, "");

  if (in_array($hero, $heroes) && in_array($weapon1, $weapons) && in_array($weapon2, $weapons) && in_array($weapon3, $weapons) && in_array($weapon4, $weapons) && in_array($weapon5, $weapons) && in_array($weapon6, $weapons)) {
    // legal deck
    $deck = array($user, $hero, $weapon1, $weapon2, $weapon3, $weapon4, $weapon5, $weapon6);
    $json_data = file_get_contents('data/tournaments.json');
    $decoded = json_decode($json_data, true);
    //$decoded->{$tid}->players->{$user} = $deck;
    //$encoded = json_encode($decoded);

    $tournamentData = $decoded[$tid];
    $players = $tournamentData["players"];
    $players[$login] = $deck;

    //var_dump($players);

    $newdata = array("id" => $tid, "limit" => $limit, "organizer" => $org, "players" => $players);


    $decoded[$tid] = $newdata;
    //$players[$user] = $deck;
    //$tournamentData["players"] = $players;
    //$decoded[$tid] = $tournamentData;

    $encoded = json_encode($decoded);
    $myfile = fopen("data/tournaments.json", "w") or die("Unable to open file!");
    fwrite($myfile, $encoded);
    fclose($myfile);

    header("Location: done.php");
    die();
    
  } else {
    echo "Error: Tento balíček není legální, prosím vyberte karty ze seznamu <br>";
  }


}

if ($_POST["import"] == "app") {
  //echo "app import";
  $appid = $_COOKIE["login"];

$url = "https://www.scratchwars.cloud/public/api/user/$appid/inventory";

$heroid = apiGet($url)->response->selected_hero;
//echo "heroid: $heroid <br>";

$url = "https://www.scratchwars.cloud/public/api/card/$heroid";
$herodata = apiGet($url);

$hero = $herodata->response->cname->CS;
//echo "hero: $hero <br>";
$weaponIdList = $herodata->response->weapons;

$number = 0;
foreach ($weaponIdList as $index) {
    //echo "weaponId: $index <br>";
    $url = "https://www.scratchwars.cloud/public/api/card/$index";
    $weaponList[$number] = apiGet($url)->response->cname->CS;
    $number++;
}
//var_dump($weaponList);
if (isset($weaponList[0])) {
  $weapon1 = $weaponList[0];
}

if (isset($weaponList[1])) {
  $weapon2 = $weaponList[1];
}

if (isset($weaponList[2])) {
  $weapon3 = $weaponList[2];
}
if (isset($weaponList[3])) {
  $weapon4 = $weaponList[3];
}

if (isset($weaponList[4])) {
  $weapon5 = $weaponList[4];
}

if (isset($weaponList[5])) {
  $weapon6 = $weaponList[5];
}

}

if ($_POST["swolink"] !== "" && $_POST["swolink"] !== null) {
  //echo "scratch import";
  $url = $_POST["swolink"];

$lines = file($url, FILE_IGNORE_NEW_LINES);

//var_dump($lines);
//echo $lines[71];
$heroline = $lines[70];
$hero1 = substr($heroline, 43);
$hero2 = explode(" ⭐", $hero1);
$hero = $hero2[0];

$weaponLine = $lines[71];
$temp = substr($weaponLine, 4);
$temp2 = explode(" ⭐", $temp);
$weapon1 = $temp2[0];

$weaponLine = $lines[72];
$temp = substr($weaponLine, 4);
$temp2 = explode(" ⭐", $temp);
$weapon2 = $temp2[0];

$weaponLine = $lines[73];
$temp = substr($weaponLine, 4);
$temp2 = explode(" ⭐", $temp);
$weapon3 = $temp2[0];

$weaponLine = $lines[74];
$temp = substr($weaponLine, 4);
$temp2 = explode(" ⭐", $temp);
$weapon4 = $temp2[0];

$weaponLine = $lines[75];
$temp = substr($weaponLine, 4);
$temp2 = explode(" ⭐", $temp);
$weapon5 = $temp2[0];

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

* { box-sizing: border-box; }
body {
  font: 16px Arial;
}
.autocomplete {
  /*the container must be positioned relative:*/
  position: relative;
  display: inline-block;
}
input {
  border: 1px solid transparent;
  background-color: #f1f1f1;
  padding: 10px;
  font-size: 16px;
}
input[type=text] {
  background-color: #f1f1f1;
  width: 100%;
}
input[type=submit] {
  background-color: DodgerBlue;
  color: #fff;
}
.autocomplete-items {
  position: absolute;
  border: 1px solid #d4d4d4;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
}
.autocomplete-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #fff;
  border-bottom: 1px solid #d4d4d4;
}
.autocomplete-items div:hover {
  /*when hovering an item:*/
  background-color: #e9e9e9;
}
.autocomplete-active {
  /*when navigating through the items using the arrow keys:*/
  background-color: DodgerBlue !important;
  color: #ffffff;
}

form {
  float: left;
  padding: 5px;
}

</style>


</head>
<body>
    <script type="text/javascript" src="autocomplete.js"></script>
    Player: <?php echo $_COOKIE["login"]?> <br>


    <?php 

    $json_data = file_get_contents('data/tournaments.json');
    $decoded = json_decode($json_data, true);
    $tournamentData = $decoded[$_GET["tid"]];
    $limit = $tournamentData["limit"];

    $heroes = file_get_contents('heroes/'.$limit.'.json');

    $json_data = file_get_contents('data/tournaments.json');
    $decoded = json_decode($json_data, true);
    $tournamentData = $decoded[$_GET["tid"]];
    $limit = $tournamentData["limit"];

    $weapons = file_get_contents('weapons/'.$limit.'.json');
    //$decoded = json_decode($json_data, true);

    ECHO <<<END
    <script> 

    var heroes = $heroes;
    var weapons = $weapons;

    </script>
    END;
    ?>

    <form autocomplete="off" action="player.php?tid=<?php echo $_GET["tid"]?>" method="POST">
        <div class="autocomplete" style="width:250px;">
            <input id="hero" type="text" name="hero" placeholder="Tvůj Hrdina" value="<?php echo $hero;?>">
        </div>

        <div class="autocomplete" style="width:250px;">
            <input id="weapon1" type="text" name="weapon1" placeholder="Zbraň 1" value="<?php echo $weapon1;?>">
        </div>

        <div class="autocomplete" style="width:250px;">
            <input id="weapon2" type="text" name="weapon2" placeholder="Zbraň 2" value="<?php echo $weapon2;?>">
        </div>

        <div class="autocomplete" style="width:250px;">
            <input id="weapon3" type="text" name="weapon3" placeholder="Zbraň 3" value="<?php echo $weapon3;?>">
        </div>

        <div class="autocomplete" style="width:250px;">
            <input id="weapon4" type="text" name="weapon4" placeholder="Zbraň 4" value="<?php echo $weapon4;?>">
        </div>

        <div class="autocomplete" style="width:250px;">
            <input id="weapon5" type="text" name="weapon5" placeholder="Zbraň 5" value="<?php echo $weapon5;?>">
        </div>

        <div class="autocomplete" style="width:250px;">
            <input id="weapon6" type="text" name="weapon6" placeholder="Zbraň 6" value="<?php echo $weapon6;?>">
        </div>

        <?php 
        $tid = $_GET["tid"];
        echo <<<END
          <input type="hidden" id="tid" name="tid" value="$tid">
        END;
        ?>

    <input type="submit">
    </form>

    <form action="player.php?tid=<?php echo $_GET["tid"]?>" method="POST">
      <input type="hidden" name="import" value="app">
      <input type="submit" value="Importovat z appky">
    </form>

    <form action="player.php?tid=<?php echo $_GET["tid"]?>" method="POST">
      <input type="hidden" name="import" value="swo">
      <input type="text" name="swolink" placeholder="Odkaz na balíček na SWO" style="width: 300px;">
      <input type="submit" value="Importovat z swo">
    </form>

    <script>
autocomplete(document.getElementById("hero"), heroes);
autocomplete(document.getElementById("weapon1"), weapons);
autocomplete(document.getElementById("weapon2"), weapons);
autocomplete(document.getElementById("weapon3"), weapons);
autocomplete(document.getElementById("weapon4"), weapons);
autocomplete(document.getElementById("weapon5"), weapons);
autocomplete(document.getElementById("weapon6"), weapons);
</script>

</body>
</html>