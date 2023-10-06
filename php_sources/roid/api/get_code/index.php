<?php
    $att_id = $_GET['att_id'];
    $code = '';
    $b32_a='ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    for($i=0;$i<3;$i++)
    {
        $code .= $b32_a[random_int(0,31)];
    }
    $code .= '-';
    for($i=0;$i<3;$i++)
    {
        $code .= $b32_a[random_int(0,31)];
    }
    $codehash = password_hash($code,PASSWORD_DEFAULT);
    require_once "../../includes/dbh.inc.php";
    $query = "UPDATE logonattempts
    SET scanned = TRUE, two_f_a_code_hash = :chash WHERE attempt_id = :att_id;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":chash", $codehash);
    $stmt->bindParam(":att_id", $att_id);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;

    $query = "SELECT ip, os, browser FROM logonattempts WHERE attempt_id = :att_id;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":att_id", $att_id);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;
    $pdo = null;

    if(sizeof($result)>0)
    {
        echo '{"code":"' . $code . '","ip":"' . $result[0]['ip'] . '","os":"' . $result[0]['os'] . '","browser":"' . $result[0]['browser'] . '"}';
    }
    else
    {
        echo $att_id;
    }
    die();
    //echo $code;
?>