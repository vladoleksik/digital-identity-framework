<?php
    
    ini_set('session.session_use_only_cookies',1);
    ini_set('session.use_strict_mode',1);

    session_set_cookie_params([
        'lifetime' => 1800,
        'domain' => 'dummy-sp.localhost',
        'path' => '/',
        'secure' => true,
        'httponly' => true
    ]);

    session_name("auth_PMS");
    session_start();

    if(!isset($_GET['state']))
    {
        session_unset();
        session_destroy();
        //echo "No state!";
        header("Location: ../?autherror=true");
        die();
    }
    if(!isset($_GET['code']))
    {
        session_unset();
        session_destroy();
        //echo "No code!";
        header("Location: ../?autherror=true");
        die();
    }

    if($_GET['state']!=$_SESSION['state'])
    {
        session_unset();
        session_destroy();
        //echo "State problem!! :/";
        header("Location: ../?autherror=true");
        die();
    }


    //SEND A cURL POST REQUEST FOR TOKEN

    $provurl = 'https://roconnect.localhost';

    $url = $provurl . "/token/";

    //The data you want to send via POST
    $fields = [
        'grant_type'        => 'authorization_code',
        'redirect_uri'      => 'https://dummy-sp.localhost/oidc_callback',
        'client_id'         => '804d3b959d1e9e9c88f3e0c3e3af2e89247a1ef46c5d4ba251dab12897e8862b',
        'client_secret'     => 'e80ad89d438d2e010015cb872264f6885e55ac75596e0445eca3afbcf248078a',
        'code'              => $_GET['code']
    ];

    //url-ify the data for the POST
    $fields_string = http_build_query($fields);

    //open connection
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,true);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
    curl_setopt($ch, CURLOPT_CAINFO,"D:/Vlad/Proiecte/Digitalisation/SSL_conf/roconnect/server.crt");

    //So that curl_exec returns the contents of the cURL; rather than echoing it
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

    //execute post
    $result = curl_exec($ch);

    curl_close($ch);

    if(!$result)
    {
        session_unset();
        session_destroy();
        //echo "No answer!";
        header("Location: ../?autherror=true");
        die();
    }

    $tokens_json = json_decode($result, true);


    //echo $result;
    //die();

    if(json_last_error() != JSON_ERROR_NONE)
    {
        session_unset();
        session_destroy();
        //echo "Wrong json!";
        header("Location: ../?autherror=true");
        die();
    }

    //echo $tokens_json;

    $id_token = $tokens_json['id_token'];
    $access_token = $tokens_json['access_token'];
    $token_type = $tokens_json['token_type'];
    $exp = $tokens_json['expires_in'];

    $jwt_components = explode(".", $id_token);

    $jwtheader = base64_decode(str_replace(['-', '_'], ['+', '/'], $jwt_components[0]));
    $jwtbody = str_replace('\/','/',base64_decode($jwt_components[1]));
    $jwtsignature = base64_decode(str_replace(['-', '_'], ['+', '/'], $jwt_components[2]));

    $public_key = openssl_pkey_get_public("file://roconnect.pem");
    //$public_key = openssl_pkey_get_public($_SESSION['provider_url'] . '/.well-known/public.key');
    //var_dump($public_key);
    //die();
    $integ = openssl_verify("$jwt_components[0].$jwt_components[1]",$jwtsignature,$public_key,"sha256WithRSAEncryption");
    //echo "Before integ<br/>";
    if($integ!=1)
    {
        session_unset();
        session_destroy();
        echo "Err: Integ<br/>";
        //header("Location: ../?autherror=true");
        die();
    }
    $jwtclaims = json_decode($jwtbody, true);
    //print_r($jwtclaims);
    if($jwtclaims['nonce']!=$_SESSION['nonce'])
    {
        session_unset();
        session_destroy();
        echo "Err: Decode<br/>";
        //header("Location: ../?autherror=true");
        die();
    }
    //echo "Before url<br/>";
    if($jwtclaims['iss']!=$provurl)
    {
        session_unset();
        session_destroy();
        echo "Err: URL<br/>";
        //header("Location: ../?autherror=true");
        die();
    }
    //echo "Before aud<br/>";
    $clientid = '804d3b959d1e9e9c88f3e0c3e3af2e89247a1ef46c5d4ba251dab12897e8862b';
    if($jwtclaims['aud']!=$clientid && (!in_array($clientid, $jwtclaims['aud']) || $jwtclaims['azp']!=$clientid))
    {
        session_unset();
        session_destroy();
        echo "Err: Aud<br/>";
        //header("Location: ../?autherror=true");
        die();
    }
    if($jwtclaims['exp']<time())
    {
        session_unset();
        session_destroy();
        echo "Expired token!";
        //header("Location: ../?autherror=true");
        die();
    }
    //echo "All the way here!<br/>";

    $sub = $jwtclaims['sub'];

    //PERFORM GET REQUEST FOR USER DATA

    $url = $provurl . "/userinfo/?schema=openid";

    $ch = curl_init();

    //set the url, headers
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $access_token
    ));
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,true);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
    curl_setopt($ch, CURLOPT_CAINFO,"D:/Vlad/Proiecte/Digitalisation/SSL_conf/roconnect/server.crt");
    //So that curl_exec returns the contents of the cURL; rather than echoing it
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

    //execute post
    $result = curl_exec($ch);
    curl_close($ch);
    //echo $result;
    //die();
    $userdata = json_decode($result, true);
    $redpos = $_SESSION['red_pos'];
    $state = $_SESSION['state'];
    $scopes = 'openid given_name family_name cnp preferred_username birthdate gender serie_id';
    session_unset();
    session_destroy();

    ini_set('session.session_use_only_cookies',1);
    ini_set('session.use_strict_mode',1);

    session_set_cookie_params([
        'lifetime' => $exp,
        'domain' => 'dummy-sp.localhost',
        'path' => '/',
        'secure' => true,
        'httponly' => true
    ]);

    session_name("session_PMS");
    session_start();
    session_regenerate_id(true);
    session_unset();

    $scopes = explode(' ', $scopes);


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
                    echo "Missing data!";
                    //header("Location: ../?autherror=true");
                    die();
                }
            }
        }
        $_SESSION['pers']=$userdata['metadata']['pers'];
        $_SESSION['document']=$userdata['metadata']['docutype'];
        $_SESSION['eidas_level']=$userdata['metadata']['eidas_level'];
        $_SESSION['state']=$state;
        $_SESSION['id_type'] = "CI";
        $_SESSION['id_batch'] = substr($_SESSION['serie_id'],0,-6);
        $_SESSION['id_sn'] = substr($_SESSION['serie_id'],-6);
        header("Location: " . $redpos);
        die();
    }
    else
    {
        session_unset();
        session_destroy();
        echo "Sub not matching!";
        //header("Location: ../?autherror=true");
        die();
    }
    
?>