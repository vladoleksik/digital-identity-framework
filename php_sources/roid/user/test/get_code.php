<?php
    ini_set('session.session_use_only_cookies',1);
    ini_set('session.use_strict_mode',1);

    session_set_cookie_params([
        'lifetime' => 1800,
        'domain' => 'ro-id.localhost',
        'path' => '/user/',
        'secure' => true,
        'httponly' => true
    ]);

    session_name("Ro-ID_auth");
    session_start();
    $att_id = $_SESSION['att_id'];
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
    $pdo = null;

    echo $code;
?>