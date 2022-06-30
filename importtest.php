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

$url = "https://scratchwars-online.cz/cs/account/decks/9990";

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


echo $hero;
echo "<br>";
echo "$weapon1 <br>";
echo "$weapon2 <br>";
echo "$weapon3 <br>";
echo "$weapon4 <br>";
echo "$weapon5 <br>";


//$tags = get_meta_tags($url);
//var_dump($tags);



/*
$appid = "mole-7050";

$url = "https://www.scratchwars.cloud/public/api/user/$appid/inventory";

$heroid = apiGet($url)->response->selected_hero;
echo "heroid: $heroid <br>";

$url = "https://www.scratchwars.cloud/public/api/card/$heroid";
$herodata = apiGet($url);

$hero = $herodata->response->cname->CS;
echo "hero: $hero <br>";
$weaponIdList = $herodata->response->weapons;

$number = 0;
foreach ($weaponIdList as $index) {
    echo "weaponId: $index <br>";
    $url = "https://www.scratchwars.cloud/public/api/card/$index";
    $weaponList[$number] = apiGet($url)->response->cname->CS;
    $number++;
}
var_dump($weaponList);
*/
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
    
</body>
</html>