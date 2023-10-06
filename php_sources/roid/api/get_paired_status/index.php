<?php
require_once "../../includes/dbh.inc.php";
$query = "SELECT * FROM logonattempts WHERE attempt_id = :att_id AND logged = TRUE;";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":att_id", $_GET['att_id']);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = null;
$pdo = null;
if(sizeof($result)>0)
{
    echo "1";
}
else
{
    echo "0";
}
die();
?>