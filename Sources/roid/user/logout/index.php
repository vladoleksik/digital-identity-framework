<?php
$redir_url = "javascript:history.go(-1)";
if(isset($_SERVER['HTTP_REFERER'])) {
    $redir_url = $_SERVER['HTTP_REFERER'];
}
if(isset($_GET['post_logout_redirect_uri']))
{
    $redir_url = urldecode($_GET['post_logout_redirect_uri']);
}

$logoutdone = false;
if(isset($_GET['id_token_hint']))
{
    $jwt = $_GET['id_token_hint'];
    $jwt_components = explode(".", $atjwt);

    $jwtheader = base64_decode(str_replace(['-', '_'], ['+', '/'], $jwt_components[0]));
    $jwtbody = str_replace('\/','/',base64_decode($jwt_components[1]));
    $jwtsignature = base64_decode(str_replace(['-', '_'], ['+', '/'], $jwt_components[2]));

    $public_key = openssl_pkey_get_public("file://../../.well-known/public.key");

    $integ = openssl_verify("$jwt_components[0].$jwt_components[1]",$jwtsignature,$public_key,"sha256WithRSAEncryption");

    if($integ==1)
    {
        $jwtclaims = json_decode($jwtbody, true);
        $sub_to_log_out = $jwtclaims['sub'];
        require_once "../../includes/dbh.inc.php";
        $query = "DELETE FROM logonattempts WHERE sub = :sub;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":sub", $sub_to_log_out);
        $stmt->execute();
        $deleted = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;

        $query = "DELETE FROM accesstokens WHERE FK_sub = :sub;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":sub", $sub_to_log_out);
        $stmt->execute();
        $deleted = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;

        $query = "DELETE FROM refreshtokens WHERE FK_sub = :sub;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":sub", $sub_to_log_out);
        $stmt->execute();
        $deleted = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;

        $query = "DELETE FROM loggedin WHERE sub = :sub;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":sub", $sub_to_log_out);
        $stmt->execute();
        $deleted = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;
        $pdo = null;

        ini_set('session.session_use_only_cookies',1);
        ini_set('session.use_strict_mode',1);

        session_set_cookie_params([
            'lifetime' => 1800,
            'domain' => 'localhost',
            'path' => '/ro-id/user/',
            'secure' => true,
            'httponly' => true
        ]);

        session_name("Ro-ID_auth");
        session_start();
        session_unset();
        session_destroy();

        $logoutdone = true;
        header("Location: " . $redir_url);
        die();
    }
    
}

if((!$logoutdone) && isset($_GET['state']))
{
    $state = $_GET['state'];
    require_once "../../includes/dbh.inc.php";
    $query = "SELECT sub FROM loggedin WHERE state = :state;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":state", $state);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = null;

    if(sizeof($result)>0)
    {
        $sub_to_log_out = $result[0]['sub'];
        

        $query = "DELETE FROM accesstokens WHERE FK_sub = :sub;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":sub", $sub_to_log_out);
        $stmt->execute();
        $deleted = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;

        $query = "DELETE FROM refreshtokens WHERE FK_sub = :sub;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":sub", $sub_to_log_out);
        $stmt->execute();
        $deleted = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;

        $query = "DELETE FROM logonattempts WHERE sub = :sub;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":sub", $sub_to_log_out);
        $stmt->execute();
        $deleted = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;


        $query = "DELETE FROM loggedin WHERE sub = :sub;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":sub", $sub_to_log_out);
        $stmt->execute();
        $deleted = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;
        $pdo = null;


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
        session_unset();
        session_destroy();

        $logoutdone = true;
        header("Location: " . $redir_url);
        die();
    }
}

if(!$logoutdone)
{
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
    session_unset();
    session_destroy();
    header("Location: " . $redir_url);
    die();
}

?>