<?php
require_once "../../includes/dbh.inc.php";
$query = "DELETE FROM logonattempts WHERE attempt_id = :att_id;";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":att_id", $_GET['att_id']);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = null;
$pdo = null;
die();
?>