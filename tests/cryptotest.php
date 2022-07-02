<?php 
error_reporting(0);
$key = "1234";

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

echo Encrypted("Ahoj tady mole");
echo "<br> toto byl encrypt tedka bude decrypt <br>";
echo Decrypted("EzMrxvB8gtM2foF4w2lDRQ==");

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