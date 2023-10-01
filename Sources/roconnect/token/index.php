<?php
require_once "../includes/dbh.inc.php";

if(!isset($_POST['client_id']))
{
  echo "Next time.";
  die();
}

$claimedclient = $_POST['client_id'];

$query = "SELECT ClientSecretHash FROM service_providers WHERE ClientID = :client_id;";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":client_id", $claimedclient);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = null;

if(sizeof($result)<1)
{
  echo "Access denied!";
  die();
}

$clientsecrethash = $result[0]['ClientSecretHash'];

if(!password_verify(hex2bin($_POST['client_secret']), $clientsecrethash))
{
  echo "Access denied!";
  die();
}

//remove expired and redeemed codes from database
$query = "DELETE FROM authzcodes WHERE redeemed = TRUE OR iat <= :last_time;";
$stmt = $pdo->prepare($query);
$last_iat = date('Y-m-d H:i:s', time()-30);
$stmt->bindParam(":last_time", $last_iat);
$stmt->execute();
$deleted = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = null;

//get acces token id
$query = "SELECT FK_accesstoken FROM authzcodes WHERE code = :code;";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":code", $_POST['code']);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = null;

if(sizeof($result)<1)
{
  echo "Access denied!";
  die();
}

$atid = $result[0]['FK_accesstoken'];



$query = "DELETE FROM authzcodes WHERE code = :code;";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":code", $_POST['code']);
$stmt->execute();
$deleted = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = null;

/*if($_POST['code']!='123')
{
  echo "Access denied!";
  die();
}*/


//get sub
$query = "SELECT FK_sub FROM accesstokens WHERE token_id = :atid;";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":atid", $atid);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = null;

if(sizeof($result)<1)
{
  echo "Oops...";
  die();
}
$sub = $result[0]['FK_sub'];

//get nonce
$query = "SELECT nonce, FK_ClientID FROM loggedin WHERE sub = :sub;";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":sub", $sub);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = null;

if(sizeof($result)<1)
{
  echo "Oops...";
  die();
}
$nonce = $result[0]['nonce'];
$client_id = $result[0]['FK_ClientID'];

if($client_id != $claimedclient)
{
  echo "Access denied!";
  die();
}

//issue an id token
$header = json_encode(['typ' => 'JWT', 'alg' => 'RS256']);

$time_now = time();

$payload = json_encode([
    'iss' => 'https://roconnect.localhost',
    'sub' => $sub,
    'aud' => $client_id,
    'nonce' => $nonce,
    'exp' => $time_now+1000,
    'iat' => $time_now,
]);

$base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
$base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

$private_key = openssl_pkey_get_private("file://D:/Vlad/Proiecte/Digitalisation/Keys/roconnect.key");

//var_dump($private_key);

openssl_sign(
    "$base64UrlHeader.$base64UrlPayload",
    $signature,
    $private_key,
    "sha256WithRSAEncryption"
);

$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
$jwt = "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";

//issue an access token
$atheader = json_encode(['typ' => 'JWT', 'alg' => 'RS256']);

$atpayload = json_encode([
    'client_id' => $client_id,
    'iss' => 'https://roconnect.localhost',
    'sub' => $sub,
    'aud' => $client_id,
    'exp' => $time_now+60,
    'iat' => $time_now,
    'jti' => $atid
]);

$atbase64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($atheader));
$atbase64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($atpayload));

openssl_sign(
  "$atbase64UrlHeader.$atbase64UrlPayload",
  $atsignature,
  $private_key,
  "sha256WithRSAEncryption"
);

$atbase64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($atsignature));
$atjwt = "$atbase64UrlHeader.$atbase64UrlPayload.$atbase64UrlSignature";

openssl_free_key($private_key);



$stmt = null;

echo '{
    "access_token": "'. $atjwt .'",
    "token_type": "Bearer",
    "expires_in": 3600,
    "id_token": "'. $jwt .'"
  }';

$pdo = null;

?>