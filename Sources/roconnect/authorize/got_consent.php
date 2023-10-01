<?php
      ini_set('session.session_use_only_cookies',1);
      ini_set('session.use_strict_mode',1);
  
      session_set_cookie_params([
          'lifetime' => 1800,
          'domain' => 'roconnect.localhost',
          'path' => '/',
          'secure' => true,
          'httponly' => true
      ]);
  
      session_name("Ro-Connect_session");
      session_start();

      $sub = $_SESSION['sub'];

      require_once "../includes/dbh.inc.php";
      /*$query = "SELECT token_id FROM accesstokens WHERE FK_sub = :sub";
      $stmt = $pdo->prepare($query);
      $stmt->bindParam(":sub", $sub);
      $stmt->execute();
      $tids = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $stmt = null;

      foreach($tids as &$tid)
      {
        $query = "DELETE FROM authzcodes WHERE FK_accesstoken = :tid";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":tid", $tid['token_id']);
        $stmt->execute();
        $deleted = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $stmt = null;
      }

      $query = "DELETE FROM accesstokens WHERE FK_sub = :sub";
      $stmt = $pdo->prepare($query);
      $stmt->bindParam(":sub", $sub);
      $stmt->execute();
      $tids = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $stmt = null;*/

      $query = "DELETE FROM loggedin WHERE sub = :sub AND FK_ClientID = :cl_id";
      $stmt = $pdo->prepare($query);
      $stmt->bindParam(":sub", $sub);
      $stmt->bindParam(":cl_id", $_GET['client_id']);
      $stmt->execute();
      $inserted = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
      $stmt = null;

      $query = "INSERT INTO loggedin (sub, preferred_username, given_name, first_name, gender, birthdate, cnp, serie_id, nonce, state, FK_ClientID)
                VALUES (:sub, :pun, :gn, :fn, :gnd, :bd, :cnp, :id, :nonce, :state, :cid);";
      $genders = array('male' => 'M', 'female' => 'F');
      $stmt = $pdo->prepare($query);
      //$gender = 'M';
      $stmt->bindParam(":sub", $sub);
      $stmt->bindParam(":pun", $_SESSION['preferred_username']);
      $stmt->bindParam(":gn", $_SESSION['given_name']);
      $stmt->bindParam(":fn", $_SESSION['family_name']);
      $stmt->bindParam(":gnd", $genders[$_SESSION['gender']]);
      $stmt->bindParam(":bd", $_SESSION['birthdate']);
      $stmt->bindParam(":cnp", $_SESSION['cnp']);
      $stmt->bindParam(":id", $_SESSION['serie_id']);
      $stmt->bindParam(":nonce", $_GET['nonce']);
      $stmt->bindParam(":state", $_GET['state']);
      $stmt->bindParam(":cid", $_GET['client_id']);
      $stmt->execute();
      $inserted = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
      $stmt = null;


      $tid = bin2hex(random_bytes(32));
      $iat = date( "Y-m-d H:i:s", time());

      $query = "INSERT INTO accesstokens (token_id, iat, FK_sub) VALUES (:tid, :iat, :sub);";
      $stmt = $pdo->prepare($query);
      $stmt->bindParam(":tid", $tid);
      $stmt->bindParam(":iat", $iat);
      $stmt->bindParam(":sub", $sub);
      $stmt->execute();
      $inserted = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $stmt = null;

      //generate an authorization code, an access token, and an id token, store them in the database etc.
      $authz_code = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(random_bytes(32)));
      //store the authz code in the database
      $query = "INSERT INTO authzcodes (code, iat, FK_accesstoken) VALUES (:code, :iat, :atid);";
      $stmt = $pdo->prepare($query);
      $stmt->bindParam(":code", $authz_code);
      $stmt->bindParam(":iat", $iat);
      $stmt->bindParam(":atid", $tid);
      $stmt->execute();
      $inserted = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $stmt = null;
      $pdo = null;

      header("Location: " . urldecode($_GET['redirect_uri'] . '?code=' . $authz_code . '&state=' . $_GET['state']));
      die();

    ?>