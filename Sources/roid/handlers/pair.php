<?php

if($_SERVER['REQUEST_METHOD'] != "POST")
{
    header("HTTP/1.1 403 Forbidden");
    die();
}

$code = $_POST['code'];
$att_id = $_POST['att_id'];


//check with the database if the code is correct and update status
//send the user to the previous page anyway
require_once "../includes/dbh.inc.php";
$query = "SELECT two_f_a_code_hash FROM logonattempts WHERE attempt_id = :att_id;";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":att_id", $att_id);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = null;

if(sizeof($result)<1)
{
    echo 'Login error. Go back and cancel to retry.';
    die();
}

$codehash = $result[0]['two_f_a_code_hash'];
if(password_verify($code,$codehash))
{
    $query = "UPDATE logonattempts
    SET logged = TRUE WHERE attempt_id = :att_id;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":att_id", $att_id);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;

}

$previous = "javascript:history.go(-1)";
if(isset($_SERVER['HTTP_REFERER'])) {
    $previous = $_SERVER['HTTP_REFERER'];
}

header("Location: ".$previous);
die();

?>