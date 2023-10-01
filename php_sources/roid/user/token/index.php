<?php
if($_POST['client_id'] != '412800e26513f4f0a7818a6ff1aa866ba2308bbbf607fa14716467238ca8c951')
{
  echo "Access denied!";
  die();
}
if(!password_verify(hex2bin($_POST['client_secret']), '$2y$10$cHQkl9gP2fNyGDs36M7cbO.fu1lTo55SJUo6BfbXaoUkfiqswGMSC'))
{
  echo "Access denied!";
  die();
}

//remove expired and redeemed codes from database
require_once "../../includes/dbh.inc.php";
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
$query = "SELECT nonce FROM loggedin WHERE sub = :sub;";
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

//issue an id token
$header = json_encode(['typ' => 'JWT', 'alg' => 'RS256']);

$time_now = time();

$payload = json_encode([
    'iss' => 'https://ro-id.localhost',
    'sub' => $sub,
    'aud' => '412800e26513f4f0a7818a6ff1aa866ba2308bbbf607fa14716467238ca8c951',
    'nonce' => $nonce,
    'exp' => $time_now+1000,
    'iat' => $time_now,
]);

$base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
$base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

$private_key = openssl_pkey_get_private("file://D:/Vlad/Proiecte/Digitalisation/Keys/roid.key");

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
    'client_id' => '412800e26513f4f0a7818a6ff1aa866ba2308bbbf607fa14716467238ca8c951',
    'iss' => 'https://ro-id.localhost',
    'sub' => $sub,
    'aud' => '412800e26513f4f0a7818a6ff1aa866ba2308bbbf607fa14716467238ca8c951',
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

//issue a refresh token
$rtheader = json_encode(['typ' => 'JWT', 'alg' => 'RS256']);
$rtid = bin2hex(random_bytes(64));

$rtpayload = json_encode([
    'client_id' => '412800e26513f4f0a7818a6ff1aa866ba2308bbbf607fa14716467238ca8c951',
    'iss' => 'https://ro-id.localhost',
    'sub' => $sub,
    'aud' => '412800e26513f4f0a7818a6ff1aa866ba2308bbbf607fa14716467238ca8c951',
    'exp' => $time_now+3600,
    'iat' => $time_now,
    'jti' => $rtid
]);

$rtbase64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($rtheader));
$rtbase64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($rtpayload));

openssl_sign(
  "$rtbase64UrlHeader.$rtbase64UrlPayload",
  $rtsignature,
  $private_key,
  "sha256WithRSAEncryption"
);

$rtbase64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($rtsignature));
$rtjwt = "$rtbase64UrlHeader.$rtbase64UrlPayload.$rtbase64UrlSignature";

openssl_free_key($private_key);

//register refresh token
$iat = date( "Y-m-d H:i:s", time());

$query = "INSERT INTO refreshtokens (token_id, iat, FK_sub) VALUES (:tid, :iat, :sub);";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":tid", $rtid);
$stmt->bindParam(":iat", $iat);
$stmt->bindParam(":sub", $sub);
$stmt->execute();
$inserted = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = null;

echo '{
    "access_token": "'. $atjwt .'",
    "token_type": "Bearer",
    "expires_in": 3600,
    "refresh_token": "'. $rtjwt .'",
    "id_token": "'. $jwt .'"
  }';

$pdo = null;

?>