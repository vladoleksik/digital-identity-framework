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
    $preferred_username = 'Vlad-Andrei Oleksik';
    $birthdate = '2004-11-11';
    $given_name = 'Vlad-Andrei';
    $family_name = 'Oleksik';
    $cnp = '5041111324780';
    $serie_id = 'SR043645';
    $gender = 'M';
    $att_id = $_SESSION['att_id'];
    $sub = hash('SHA256',$cnp);

    require_once "../../includes/dbh.inc.php";
    $query = "UPDATE logonattempts
    SET sub = :sub WHERE attempt_id = :att_id;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":sub", $sub);
    $stmt->bindParam(":att_id", $att_id);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;

    $query = "DELETE FROM accesstokens WHERE FK_sub = :sub;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":sub", $sub);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;

    $query = "DELETE FROM refreshtokens WHERE FK_sub = :sub;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":sub", $sub);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;

    $query = "DELETE FROM loggedin WHERE sub = :sub;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":sub", $sub);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;

    $query = "INSERT INTO loggedin (sub, preferred_username, given_name, first_name, gender, birthdate, cnp, serie_id, FK_logon_attempt_id, state, nonce)
    VALUES (:sub, :pun, :gn, :fn, :gnd, :bd, :cnp, :id, :att_id, '-', '-');";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":sub", $sub);
    $stmt->bindParam(":pun", $preferred_username);
    $stmt->bindParam(":gn", $given_name);
    $stmt->bindParam(":fn", $family_name);
    $stmt->bindParam(":gnd", $gender);
    $stmt->bindParam(":bd", $birthdate);
    $stmt->bindParam(":cnp", $cnp);
    $stmt->bindParam(":id", $serie_id);
    $stmt->bindParam(":att_id", $att_id);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;

?>