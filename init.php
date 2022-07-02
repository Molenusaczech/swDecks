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

if ($_POST["password"] !== "" && $_POST["password"] !== null) {
    $url = "https://scratchwars.cloud/public/api/auth";
    $password = $_POST["password"];

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
    $headers = array(
       "Content-Type: application/x-www-form-urlencoded",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    
    $data = "uid=mole-7050&pin=$password";
    
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    
    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    
    $resp = curl_exec($curl);
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($code == 200) {
        $json_data = file_get_contents('data/logins.json');
        $json_data = Decrypted($json_data);
        $decoded = json_decode($json_data, true);

        $decoded["mole-7050"]["admin"] = true;
        $decoded["mole-7050"]["organizer"] = true;
        $json_data = json_encode($decoded);
        $json_data = Encrypted($json_data);
        file_put_contents('data/logins.json', $json_data);
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
</head>
<body>

<form action="init.php" method="post">
            <input type="password" name="password" placeholder="PIN"> <br>
            <input type="submit" value="Init">
</form>
    
</body>
</html>