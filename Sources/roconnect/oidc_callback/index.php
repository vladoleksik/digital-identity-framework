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

    session_name("Ro-Connect_auth");
    session_start();

    if(!isset($_GET['state']))
    {
        session_unset();
        session_destroy();
        header("Location: ../error/?err=E200001");
        die();
    }
    if(!isset($_GET['code']))
    {
        session_unset();
        session_destroy();
        header("Location: ../error/?err=E200001");
        die();
    }

    if($_GET['state']!==$_SESSION['state'])
    {
        session_unset();
        session_destroy();
        //echo "State problem!! :/";
        header("Location: ../error/?err=E200002");
        die();
    }


    //SEND A cURL POST REQUEST FOR TOKEN

    $url = $_SESSION['provider_url'] . "/user/token/";

    //The data you want to send via POST
    $fields = [
        'grant_type'        => 'authorization_code',
        'redirect_uri'      => 'https://roconnect.localhost/oidc_callback',
        'client_id'         => '412800e26513f4f0a7818a6ff1aa866ba2308bbbf607fa14716467238ca8c951',
        'client_secret'     => '0b3380b12bc9943daa94a81aaab2a0a3a1586c51f6d514698742c7a6aa206fa9',
        'code'              => $_GET['code']
    ];

    //url-ify the data for the POST
    $fields_string = http_build_query($fields);

    //open connection
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_CAINFO,"D:/Vlad/Proiecte/Digitalisation/SSL_conf/roid/server.crt");
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

    //So that curl_exec returns the contents of the cURL; rather than echoing it
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

    //execute post
    $result = curl_exec($ch);

    curl_close($ch);

    if(!$result)
    {
        session_unset();
        session_destroy();
        header("Location: ../error/?err=E200003");
        die();
    }

    $tokens_json = json_decode($result, true);




    if(json_last_error() != JSON_ERROR_NONE)
    {
        session_unset();
        session_destroy();
        header("Location: ../error/?err=E200004");
        die();
    }

    //echo $tokens_json;

    $id_token = $tokens_json['id_token'];
    $access_token = $tokens_json['access_token'];
    $refresh_token = $tokens_json['refresh_token'];
    $token_type = $tokens_json['token_type'];
    $exp = $tokens_json['expires_in'];

    $jwt_components = explode(".", $id_token);

    $jwtheader = base64_decode(str_replace(['-', '_'], ['+', '/'], $jwt_components[0]));
    $jwtbody = str_replace('\/','/',base64_decode($jwt_components[1]));
    $jwtsignature = base64_decode(str_replace(['-', '_'], ['+', '/'], $jwt_components[2]));

    $public_key = openssl_pkey_get_public("file://" . $_SESSION['provider_name'] . ".pem");
    //$public_key = openssl_pkey_get_public($_SESSION['provider_url'] . '/.well-known/public.key');
    //var_dump($public_key);
    //die();
    $integ = openssl_verify("$jwt_components[0].$jwt_components[1]",$jwtsignature,$public_key,"sha256WithRSAEncryption");
    //echo "Before integ<br/>";
    if($integ!=1)
    {
        session_unset();
        session_destroy();
        //echo "Err: Integ<br/>";
        header("Location: ../error/?err=E200002");
        die();
    }
    $jwtclaims = json_decode($jwtbody, true);
    //print_r($jwtclaims);
    if($jwtclaims['nonce']!=$_SESSION['nonce'])
    {
        session_unset();
        session_destroy();
        //echo "Err: Decode<br/>";
        header("Location: ../error/?err=E200002");
        die();
    }
    //echo "Before url<br/>";
    if($jwtclaims['iss']!=$_SESSION['provider_url'])
    {
        session_unset();
        session_destroy();
        //echo "Err: URL<br/>";
        header("Location: ../error/?err=E200002");
        die();
    }
    //echo "Before aud<br/>";
    $clientid = '412800e26513f4f0a7818a6ff1aa866ba2308bbbf607fa14716467238ca8c951';
    if($jwtclaims['aud']!=$clientid && (!in_array($clientid, $jwtclaims['aud']) || $jwtclaims['azp']!=$clientid))
    {
        session_unset();
        session_destroy();
        //echo "Err: Aud<br/>";
        header("Location: ../error/?err=E200002");
        die();
    }
    if($jwtclaims['exp']<time())
    {
        session_unset();
        session_destroy();
        header("Location: ../error/?err=E200005");
        die();
    }
    //echo "All the way here!<br/>";

    $sub = $jwtclaims['sub'];

    //PERFORM GET REQUEST FOR USER DATA

    $url = $_SESSION['provider_url'] . "/api/user/?schema=openid";

    $ch = curl_init();

    //set the url, headers
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $access_token
    ));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_CAINFO,"D:/Vlad/Proiecte/Digitalisation/SSL_conf/roid/server.crt");
    //So that curl_exec returns the contents of the cURL; rather than echoing it
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

    //execute post
    $result = curl_exec($ch);
    curl_close($ch);
    //echo $result;
    //die();
    $userdata = json_decode($result, true);
    $getparams = $_SESSION['getparams'];
    $state = $_SESSION['state'];
    $scopes = $_SESSION['scope'];
    $provurl = $_SESSION['provider_url'];
    session_unset();
    session_destroy();

    ini_set('session.session_use_only_cookies',1);
    ini_set('session.use_strict_mode',1);

    session_set_cookie_params([
        'lifetime' => $exp,
        'domain' => 'roconnect.localhost',
        'path' => '/',
        'secure' => true,
        'httponly' => true
    ]);

    session_name("Ro-Connect_session");
    session_start();
    session_regenerate_id(true);
    session_unset();

    $scopes = explode(' ', $scopes);

    //print_r($result);
    //die();

    if($userdata['sub']==$sub)
    {
        $_SESSION['sub']=$sub;
        foreach($scopes as &$scope)
        {
            if($scope!='openid')
            {
                if(isset($userdata[$scope]))
                {
                    $_SESSION[$scope] = $userdata[$scope];
                }
                else
                {
                    session_unset();
                    session_destroy();
                    header("Location: ../error/?err=E200006");
                    die();
                }
            }
        }
        $_SESSION['pers']=$userdata['metadata']['pers'];
        $_SESSION['document']=$userdata['metadata']['docutype'];
        $_SESSION['eidas_level']=$userdata['metadata']['eidas_level'];
        $_SESSION['state']=$state;
        $_SESSION['provider_url']=$provurl;
        header("Location: ../authorize/?".base64_decode(urldecode($getparams)));
    }
    else
    {
        session_unset();
        session_destroy();
        header("Location: ../error/?err=E200002");
        die();
    }
    
?>