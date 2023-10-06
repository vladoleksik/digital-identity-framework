<?php
    if(!isset($_POST['pers_data']) || !isset($_POST['certificate']) || !isset($_POST['signature']) || !isset($_POST['att_id']))
    {
        echo "Nope!";
        die();
    }
    $json = $_POST['pers_data'];
    $certificate = base64_decode($_POST['certificate']);
    $signature = $_POST['signature'];
    $certificate_data = openssl_x509_parse($certificate);
    $trusted_cert = openssl_x509_read(file_get_contents('/path/to/trusted.crt'));
    $trustedPublicKey = openssl_pkey_get_public($trusted_cert);
    $cardPublicKey = openssl_pkey_get_public(openssl_x509_read($certificate));
    if(!openssl_x509_verify($certificate, $trustedPublicKey))
    {
        echo "Denied!";
        die();
    }

    require_once "../../includes/dbh.inc.php";
    $query = "SELECT challenge FROM logonattempts WHERE attempt_id = :att_id;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":att_id", $_POST['att_id']);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;

    if(sizeof($result)==0)
    {
        echo "Error!";
        die();
    }
    if(!openssl_verify($result[0]['challenge'],hex2bin($signature)))
    {
        echo "Denied!";
        die();
    }

    $cn = $certificate_data['subject']['CN'];
    $pivot_data = explode(',',$cn);
    $family_name = $pivot_data[0];
    $given_name = $pivot_data[1];
    $cnp = $pivot_data[2];
    $sub = hash("SHA-256",$cnp);

    $all_data = json_decode($json, true);
    if($cnp!=$all_data['cnp'])
    {
        die();
    }
    $gender = $all_data['gender'];
    $serie_id = $all_data['serie_id'];
    $birthdate = strtotime(substr($all_data['birth'],0,10));
    $preferred_username = $given_name . " " . $family_name;

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