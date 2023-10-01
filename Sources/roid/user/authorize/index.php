<?php
    //check for all the errors/lacks in the inputs


    $userisloggingin = false;
    if($userisloggingin)
    {
        
        header("Location: https://roconnect.localhost/oidc_callback?code=123&state=".$_GET['state']);
        die();
    }
    
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
    session_regenerate_id(true);

    $continue_attempt = false;

    $ask_code = true;
    require_once "../../includes/dbh.inc.php";

    if(isset($_SESSION['att_id']))
    {
        $continue_attempt = true;

        //delete old attempts from the database
        
        $query = "DELETE FROM logonattempts WHERE scanned = FALSE AND iat <= :last_time;";
        $stmt = $pdo->prepare($query);
        $last_iat = date('Y-m-d H:i:s', time()-30);
        $stmt->bindParam(":last_time", $last_iat);
        $stmt->execute();
        $deleted = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;

        
        //search for this attempt in database
        $query = "SELECT scanned, logged, sub FROM logonattempts WHERE attempt_id = :att_id;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":att_id", $_SESSION['att_id']);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;
        if(sizeof($result)<1)
        {
            $continue_attempt = false;
        }
        else
        {

        
        //if the entry does not exist or has not followed through, then ignore it, delete it from database and regenerate credentials
        if(!($result[0]['scanned']==true))
        {
            $query = "DELETE FROM logonattempts WHERE attempt_id = :att_id;";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":att_id", $_SESSION['att_id']);
            $stmt->execute();
            $deleted = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = null;

            $continue_attempt = false;
        }
        else
        {
            //if the verification is already made, do not ask for the code and wait for authentication
            if($result[0]['logged']==true)
            {
                $ask_code = false;
                if($result[0]['sub']!=NULL)
                {   

                    $sub = $result[0]['sub'];

                    //if the user is already authenticated,
                    $query = "DELETE FROM logonattempts WHERE attempt_id = :att_id;";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(":att_id", $_SESSION['att_id']);
                    $stmt->execute();
                    $updated = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
                    $stmt = null;
                    
                    //fill in nonce and state on logon
                    $query = "UPDATE loggedin
                            SET nonce = :nonce, state = :state
                            WHERE sub = :sub;";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(":nonce", $_GET['nonce']);
                    $stmt->bindParam(":state", $_GET['state']);
                    $stmt->bindParam(":sub", $sub);
                    $stmt->execute();
                    $updated = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
                    $stmt = null;


                    //generate an access token id
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

                    //then redirect the user to the oidc callback of roconnect
                    header("Location: " . urldecode($_GET['redirect_uri']) . "?code=". $authz_code . "&state=".$_GET['state']);
                    die();
                    
                }
            }
            else
            {
                //else, ask for the code
                $ask_code = true;
            }
        }
    }
    }

    if(isset($_GET['refresh']) && $_GET['refresh']=='true')
    {
        $continue_attempt=false;
        //delete the previous attempt id from the database
        if(isset($_SESSION['att_id']))
        {
            $query = "DELETE FROM logonattempts WHERE attempt_id = :att_id;";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":att_id", $_SESSION['att_id']);
            $stmt->execute();
            $deleted = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = null;
        }
        unset($_SESSION['chal']);
        unset($_SESSION['att_id']);
        unset($_SESSION['already_tried']);
        $url = $_SERVER['REQUEST_URI'];
        $new_url = str_replace('&refresh=true','',$url);
        $new_url = str_replace('refresh=true&','',$new_url);
        $pdo = null;
        header("Location: ". $new_url);
        die();
    }

    if($continue_attempt)
    {
        $challenge = $_SESSION['chal'];
        $attempt_id = $_SESSION['att_id'];
        if(!isset($_SESSION['already_tried']))
        {
            $_SESSION['already_tried'] = false;
        }
    }
    else
    {
        unset($_SESSION['chal']);
        unset($_SESSION['att_id']);
        unset($_SESSION['already_tried']);
        $challenge = bin2hex(random_bytes(32));
        $attempt_id = bin2hex(random_bytes(32));
        $_SESSION['chal'] = $challenge;
        $_SESSION['att_id'] = $attempt_id;
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $ip = $_SERVER['REMOTE_ADDR'];
        $browser = get_browser($userAgent, true);

        $OSList = array
        (
        'Windows 3.11' => 'Win16',
        'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
        'Windows 98' => '(Windows 98)|(Win98)',
        'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
        'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
        'Windows Server 2003' => '(Windows NT 5.2)',
        'Windows Vista' => '(Windows NT 6.0)',
        'Windows 7' => '(Windows NT 7.0)',
        'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
        'Windows ME' => 'Windows ME',
        'Open BSD' => 'OpenBSD',
        'Sun OS' => 'SunOS',
        'Linux' => '(Linux)|(X11)',
        'Mac OS' => '(Mac_PowerPC)|(Macintosh)',
        'QNX' => 'QNX',
        'BeOS' => 'BeOS',
        'OS/2' => 'OS/2',
        'Search Bot'=>'(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(MSNBot)|(Ask Jeeves/Teoma)|(ia_archiver)'
        );
        foreach($OSList as $os=>$Match)
        {
        if (preg_match('/'.$Match.'/i', $userAgent))
            {
                break;
            }
        }
        //echo "You are using ".$os;
        //send to database: datetime, attempt id, challenge, ip, os, browser
        $iat = date( "Y-m-d H:i:s", time());
        $query = "INSERT INTO logonattempts (attempt_id, iat, challenge, ip, os, browser) VALUES (:att_id, :iat, :chal, :ip, :os, :browser);";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":att_id", $_SESSION['att_id']);
        $stmt->bindParam(":iat", $iat);
        $stmt->bindParam(":chal", $_SESSION['chal']);
        $stmt->bindParam(":ip", $ip);
        $stmt->bindParam(":os", $os);
        $stmt->bindParam(":browser", $browser['browser']);
        $stmt->execute();
        $inserted = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;
    }
    
    //$_SESSION['nonce'] = $_GET['nonce'];
    //$_SESSION['state'] = $_GET['state'];
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ro-ID</title>
    <link rel="icon" type="image/x-icon" href="../resources/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php
        if($continue_attempt)
        {
            if(!$ask_code)
            {
                echo '<meta http-equiv="refresh" content="5;URL=' . $_SERVER['REQUEST_URI'] . '">';
            }
        }
        else
        {
            echo '<meta http-equiv="refresh" content="30;URL=' . $_SERVER['REQUEST_URI'] . '">';
        }
    ?>
    <!--<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>-->
    <?php
        if(!$continue_attempt)
        {
            echo '<script src="https://unpkg.com/@bitjson/qr-code@1.0.2/dist/qr-code.js"></script>';
        }
    ?>
    
    <style>
        body {
            font-family: 'Montserrat', sans-serif !important;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <nav class="navbar sticky-top navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid shadow-sm py-3 px-5 mb-2 bg-body-tertiary rounded">
            <a class="navbar-brand fs-3 py-3 mb-1 me-4" style="font-weight: 700; margin-inline-start: 10%;" href="#">
                <img src="../resources/id.png" alt="Logo" height="48" class="d-inline-block align-text-middle">
                <span style="font-weight: 500;">Ro-</span>ID
            </a>
        </div>
    </nav>

    <style>
        .bi {
            vertical-align: -.125em;
            width: 1em;
            height: 1em;
        }
        .form-control::placeholder {
            color: #d4d4d4;
            opacity: 1;
        }

        .form-control:-ms-input-placeholder {
            color: #d4d4d4;
        }

        .form-control::-ms-input-placeholder {
            color: #d4d4d4;
        }
    </style>
    <div class="mx-5 mt-4">
        
        <?php

        if($continue_attempt)
        {
            if($ask_code)
            {
                echo '<div class="text-center mx-3 py-5 px-2 login-card justify-content-center shadow rounded">
                        <h2 class="mb-3" style="font-weight: 600;">Realizați asocierea cu telefonul</h2>
                        <h6 class="my-3" style="font-weight: 500;">Introduceți codul de 6 caractere afișat pe telefon în aplicația Ro-ID.</h6>
                        <div class="pt-3 mt-5 mb-3 w-75 mx-auto px-5">
                            <form id="code_input_form" action="../../handlers/pair.php" method="POST">
                                <input type="hidden" id="att_id_input" name="att_id" value="'. $_SESSION['att_id'] . '">
                                <input autocomplete="off" id="code_input" maxlength="7" name="code" onkeyup="this.value = this.value.replace(\'1\',\'I\').replace(\'0\',\'O\').replace(\'8\',\'B\').toUpperCase(); addHyphen(this);" class="mb-4 text-primary';
                                if($_SESSION['already_tried'])
                                {
                                    echo ' is-invalid';
                                }
                                echo ' text-center fs-1 form-control border-2 form-control-lg" type="text" placeholder="▉▉▉-▉▉▉" aria-label=".form-control-lg example">
                                <script>
                                    function addHyphen (element) {
                                        let ele = document.getElementById(element.id);
                                        ele = ele.value.split(\'-\').join(\'\');    // Remove dash (-) if mistakenly entered.

                                        let finalVal = ele.match(/.{1,3}/g).join(\'-\');
                                        document.getElementById(element.id).value = finalVal;
                                    }
                                </script>
                                <button type="button" onclick="var url = window.location.href; url = url + \'&refresh=true\'; window.location.replace(url);" class="fs-4 mx-4 shadow rounded-1 border-2 btn btn-outline-primary">Anulare</button>
                                <button type="submit" class="fs-4 mx-4 shadow rounded-1 border-2 btn btn-primary">Continuare</button>
                            </form>
                        </div>
                    </div>';
                    $_SESSION['already_tried']=true;
            }
            else
            {
                echo '<div class="text-center mx-3 py-5 px-2 login-card justify-content-center shadow rounded">
                        <h2 class="mb-3" style="font-weight: 600;">Perfect! Continuați de pe telefon</h2>
                        <h6 class="my-3" style="font-weight: 500;">Urmați instrucțiunile din aplicația Ro-ID.</h6>
                        <div class="text-center pt-3 mt-5 mb-3 w-75 mx-auto px-5">
                            <img src="../resources/phone.png" height="240" alt="Continuați de pe telefon"/>
                        </div>
                    </div>';
            }
        }
        else
        {
    
        echo '<div class="text-center mx-3 py-5 px-2 login-card d-flex flex-wrap justify-content-center flex-row shadow rounded">
            <div style="width: 50%; min-width: 340px;" class="pb-5 text-center">


                <qr-code 
                id="qr1"
                contents="';
                         //$challenge = bin2hex(random_bytes(16));
                         //$attempt_id = bin2hex(random_bytes(16));
                        echo htmlentities('{"challenge":"' . $challenge . '","attempt_id":"' . $attempt_id . '"}');
                        echo '"
                module-color="#000040"
                position-ring-color="#000040"
                position-center-color="#000040"
                mask-x-to-y-ratio="1"
                style="
                    width: 75%;
                    max-width: 320px;
                    height: auto;
                    margin: 0 auto;
                    background-color: #fff;
                "
                >
                <img height="48" class="p-0 m-0" src="../resources/logo.png" slot="icon" />
                </qr-code>
                
                <script>
                    document.getElementById(\'qr1\').addEventListener(\'codeRendered\', () => {
                        document.getElementById(\'qr1\').animateQRCode(\'MaterializeIn\');
                    });
                    </script>
                    
                <!--<img class="" style="width: 75%; max-width: 320px;" src="../resources/qr_example_sm.png"/>-->
            </div>
            <div style="width: 50%; min-width: 340px;">
                <h2 class="mb-3 text-primary-emphasis" style="font-weight: 600;">Conectare prin aplicația Ro-ID</h2>
                <h6 class="my-3" style="font-weight: 500;">Scanați codul alăturat cu aplicația Ro-ID și urmați instruncțiunile.</h6>
                <div style="float: right; width: 80%; min-width: 320px;" class="text-start">
                    <h5 class="mt-3 mb-2 pt-3" style="font-weight: 400;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-1-square me-2" viewBox="0 0 16 16">
                        <path d="M9.283 4.002V12H7.971V5.338h-.065L6.072 6.656V5.385l1.899-1.383h1.312Z"/>
                        <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2Zm15 0a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2Z"/>
                        </svg>Scanați codul din aplicație.
                    </h5>
                    <h5 class="my-2" style="font-weight: 400;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-2-square me-2" viewBox="0 0 16 16">
                        <path d="M6.646 6.24v.07H5.375v-.064c0-1.213.879-2.402 2.637-2.402 1.582 0 2.613.949 2.613 2.215 0 1.002-.6 1.667-1.287 2.43l-.096.107-1.974 2.22v.077h3.498V12H5.422v-.832l2.97-3.293c.434-.475.903-1.008.903-1.705 0-.744-.557-1.236-1.313-1.236-.843 0-1.336.615-1.336 1.306Z"/>
                        <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2Zm15 0a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2Z"/>
                        </svg>Introduceți codul pt asocierea cu telefonul.
                    </h5>
                    <h5 class="my-2" style="font-weight: 400;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-3-square me-2" viewBox="0 0 16 16">
                        <path d="M7.918 8.414h-.879V7.342h.838c.78 0 1.348-.522 1.342-1.237 0-.709-.563-1.195-1.348-1.195-.79 0-1.312.498-1.348 1.055H5.275c.036-1.137.95-2.115 2.625-2.121 1.594-.012 2.608.885 2.637 2.062.023 1.137-.885 1.776-1.482 1.875v.07c.703.07 1.71.64 1.734 1.917.024 1.459-1.277 2.396-2.93 2.396-1.705 0-2.707-.967-2.754-2.144H6.33c.059.597.68 1.06 1.541 1.066.973.006 1.6-.563 1.588-1.354-.006-.779-.621-1.318-1.541-1.318Z"/>
                        <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2Zm15 0a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2Z"/>
                        </svg>Introduceți PIN-ul în aplicație.
                    </h5>
                    <h5 class="my-2" style="font-weight: 400;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-4-square me-2" viewBox="0 0 16 16">
                        <path d="M7.519 5.057c.22-.352.439-.703.657-1.055h1.933v5.332h1.008v1.107H10.11V12H8.85v-1.559H4.978V9.322c.77-1.427 1.656-2.847 2.542-4.265ZM6.225 9.281v.053H8.85V5.063h-.065c-.867 1.33-1.787 2.806-2.56 4.218Z"/>
                        <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2Zm15 0a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2Z"/>
                        </svg>Atingeți cartea electronică de identitate.
                    </h5>
                    <h5 class="my-2" style="font-weight: 400;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill me-2" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>Ați terminat!
                    </h5>
                </div>
            </div>
        </div>';

        }
        ?>
    </div>

    <div class="footer container">
    <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
        <p class="col-md-4 mb-0 text-body-secondary">© 2023</p>

        <a href="#" class="col-md-3 d-flex align-items-center justify-content-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
        <img class="me-2" src="../resources/logo.png" height="32"></img>
        </a>

        <ul class="nav col-md-5 justify-content-end">
        <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Despre</a></li>
        <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Protecția datelor</a></li>
        <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Întrebări</a></li>
        <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Date tehnice</a></li>
        </ul>
    </footer>
    </div>
    
</body>
</html>

<?php
    $pdo = null;
?>