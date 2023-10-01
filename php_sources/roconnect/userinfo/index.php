<?php

  $headers = apache_request_headers();


  if(isset($headers['Authorization']))
  {
    $atjwt = preg_replace('/^Bearer /', '', $headers['Authorization']);
    $jwt_components = explode(".", $atjwt);

    $jwtheader = base64_decode(str_replace(['-', '_'], ['+', '/'], $jwt_components[0]));
    $jwtbody = str_replace('\/','/',base64_decode($jwt_components[1]));
    $jwtsignature = base64_decode(str_replace(['-', '_'], ['+', '/'], $jwt_components[2]));

    $public_key = openssl_pkey_get_public("file://../.well-known/public.pem");

    $integ = openssl_verify("$jwt_components[0].$jwt_components[1]",$jwtsignature,$public_key,"sha256WithRSAEncryption");

    if($integ!=1)
    {
      echo "Access denied!";
      die();
    }
    $jwtclaims = json_decode($jwtbody, true);
    if($jwtclaims['exp']<time())
    {
      echo "Access denied!";
      die();
    }
    $claimed_sub = $jwtclaims['sub'];
    $atid = $jwtclaims['jti'];
    require_once "../includes/dbh.inc.php";
    $query = "DELETE FROM accesstokens WHERE redeemed = TRUE OR iat <= :last_time;";
    $stmt = $pdo->prepare($query);
    $last_iat = date('Y-m-d H:i:s', time()-60);
    $stmt->bindParam(":last_time", $last_iat);
    $stmt->execute();
    $deleted = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = null;

    $query = "SELECT FK_sub FROM accesstokens WHERE token_id = :atid;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":atid", $atid);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = null;

    if(sizeof($result)<1)
    {
      echo "Access denied!";
      die();
    }

    $sub = $result[0]['FK_sub'];
    if($claimed_sub!=$sub)
    {
      echo "Ouch.";
      die();
    }

    $query = "DELETE FROM accesstokens WHERE token_id = :atid;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":atid", $atid);
    $stmt->execute();
    $deleted = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = null;

    $query = "SELECT preferred_username, given_name, first_name, gender, birthdate, cnp, serie_id FROM loggedin WHERE sub = :sub;";  //I know the db column should read 'family_name' instead of 'first_name' :/
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

    $birthdate = date('Y-m-d',strtotime($result[0]['birthdate']));
    $genders = array("M"=>"male", "F"=>"female");

    echo '{
        "given_name": "' . $result[0]['given_name'] . '",
        "family_name": "' . $result[0]['first_name'] . '",
        "birthdate": "' . $birthdate . '",
        "gender": "' . $genders[$result[0]['gender']] . '",
        "cnp": '. $result[0]['cnp'] . ',
        "preferred_username": "' . $result[0]['preferred_username'] . '",
        "serie_id": "' . $result[0]['serie_id'] . '",
        "sub": "' . $sub . '",
        "metadata" : {
          "pers" : 1,
          "docutype" : "eid",
          "eidas_level" : "eidas3"
        }

    }';
    die();
  }
  else
  {
    echo "Access denied!";
    die();
  }

  $pdo = null;

?>