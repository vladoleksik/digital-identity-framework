<?php
    $valid=true;
    if(!isset($_GET['scope']))
    {
        $valid=false;
    }
    if(!isset($_GET['response_type']) || $_GET['response_type']!='code')
    {
        $valid=false;
    }
    if(!isset($_GET['redirect_uri']))
    {
        $valid=false;
    }
    if(!isset($_GET['client_id']) || $_GET['client_id']!='412800e26513f4f0a7818a6ff1aa866ba2308bbbf607fa14716467238ca8c951')
    {
        $valid=false;
    }
    if(!isset($_GET['provider_url']))
    {
        $valid=false;
    }
    if(!isset($_GET['provider_name']))
    {
        $valid=false;
    }
    if(!isset($_GET['acr_values']))
    {
        $valid=false;
    }
    if(!isset($_GET['idp_index']))
    {
        $valid=false;
    }
    if($valid==false)
    {
        header("Location: ../error/?err=E000000");
        die();
    }
    $state = 's' . bin2hex(random_bytes(32));
    $nonce = 'n' . bin2hex(random_bytes(32));
    //$nonce = 'n88a005a0c03355e5794d3d43c247eeb9aa698b89f68d043e04e1167e297cd5d8';

    ini_set('session.session_use_only_cookies',1);
    ini_set('session.use_strict_mode',1);

    session_set_cookie_params([
        'lifetime' => 1800,
        'domain' => 'roconnect.localhost',
        'path' => '/',
        'secure' => true,
        'httponly' => true
    ]);

    session_name("Ro-Connect_auth");
    session_start();
    session_regenerate_id(true);
    $_SESSION['state']=$state;
    $_SESSION['nonce']=$nonce;
    $_SESSION['idp']=$_GET['idp_index'];
    $_SESSION['scope']=$_GET['scope'];
    $_SESSION['provider_url']=urldecode($_GET['provider_url']);
    $_SESSION['provider_name']=$_GET['provider_name'];
    $_SESSION['getparams']=$_GET['getparams'];

    header("Location: " . urldecode($_GET['provider_url']) . "/user/authorize?response_type=code&client_id=" . $_GET['client_id'] . "&redirect_uri=" . $_GET['redirect_uri'] . "&scope=" . $_GET['scope'] . "&acr_values=" . $_GET['acr_values'] . "&state=" . $state . "&nonce=" . $nonce);
    die();
?>