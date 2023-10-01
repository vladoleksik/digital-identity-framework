<?php
    $previous = "javascript:history.go(-1)";
    if(isset($_SERVER['HTTP_REFERER'])) {
        $previous = $_SERVER['HTTP_REFERER'];
    }
    if(isset($_GET['post_logout_redirect_uri']))
    {
        $previous = urldecode($_GET['post_logout_redirect_uri']);
    }
    
    if(isset($_GET['state']))
    {
        require_once "../includes/dbh.inc.php";
        $query = "DELETE FROM loggedin WHERE state = :state";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":state", $_GET['state']);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;
        $pdo = null;

    }
    ini_set('session.session_use_only_cookies',1);
    ini_set('session.use_strict_mode',1);

    session_set_cookie_params([
        'domain' => 'roconnect.localhost',
        'path' => '/',
        'secure' => true,
        'httponly' => true
    ]);
    session_name("Ro-Connect_session");
    session_start();

    if(isset($_GET['forced']))
    {
        session_unset();
        session_destroy();
        session_name("Ro-Connect_auth");
        session_start();
        session_unset();
        session_destroy();
        header('Location: ' . $previous);
        die();
    }
    else
    {
        $state = $_SESSION['state'];
        $provurl = $_SESSION['provider_url'];
        $url = $_SESSION['provider_url'] ."/user/logout?state=" . $_SESSION['state'];

        session_unset();
        session_destroy();
        session_name("Ro-Connect_auth");
        session_start();
        session_unset();
        session_destroy();

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CAINFO,"D:/Vlad/Proiecte/Digitalisation/SSL_conf/roid/server.crt");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); 

        $result = curl_exec($ch);
        curl_close($ch);
        header("Location: ".$previous);
        die();
    }
?>