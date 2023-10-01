<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ro-Connect</title>
    <link rel="icon" type="image/x-icon" href="../resources/connect.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif !important;
            font-weight: 500;
        }
    </style>
</head>
<body>
<?php
            $valid = true;
            if(!isset($_GET['client_id']))
            {
              $valid = false;
            }
            if(!isset($_GET['state']))
            {
              $valid = false;
            }
            if(!isset($_GET['nonce']))
            {
              $valid = false;
            }
            if(!isset($_GET['scope']))
            {
              $valid = false;
            }
            if(!isset($_GET['redirect_uri']))
            {
              $valid = false;
            }
            if(isset($_GET['response_type']) && $_GET['response_type']!='code')
            {
              $valid = false;
            }
            if(!$valid)
            {
              $point = "Location: ../error/?err=E100001";
              if(isset($_GET['redirect_uri_fail']))
              {
                $point .= "&redir_uri=";
                $point .= $_GET['redirect_uri_fail'];
              }
              else
              {
                if(isset($_GET['redirect_uri']))
                {
                  $point .= "&redir_uri=";
                  $point .= $_GET['redirect_uri'];
                }
                else
                {
                  $point = "Location: ../error/?err=E100000";
                }
              }
              header($point);
              die();
            }
            $redir_fail = "";
            if(isset($_GET['redirect_uri_fail']))
            {
              $redir_fail =  $_GET['redirect_uri_fail'];
            }
            else
            {
              if(isset($_GET['redirect_uri']))
              {
                $redir_fail = $_GET['redirect_uri'];
              }
            }
            $valid = true;
            $accred = "eidas1";
            if(isset($_GET['acr_values']))
            {
              if($_GET['acr_values']==='eidas2')
              {
                $accred = "eidas2";
              }
              else
              {
                if($_GET['acr_values']==='eidas3')
                {
                  $accred = "eidas3";
                }
                else
                {
                  if($_GET['acr_values']!=='eidas1')
                  {
                    $valid = false;
                  }
                }
              }
            }
            $pers = 0;
            if(isset($_GET['entitate']))
            {
              if($_GET['entitate']==="pf")
              {
                $pers = 1;
              }
              else
              {
                if($_GET['entitate']==="pj")
                {
                  $pers = 2;
                }
                else
                {
                  if($_GET['entitate']!=="any")
                  {
                    $valid = false;
                  }
                }
              }
            }
            $docutype = array();
            if(isset($_GET['id-types']))
            {
              if(str_contains($_GET['id-types'],"eid"))
              {
                array_push($docutype, "eid");
              }
              else
              {
                if(str_contains($_GET['id-types'],"c_elev"))
                {
                  array_push($docutype, "c_elev");
                }
                else
                {
                  if(str_contains($_GET['id-types'],"b_eid"))
                  {
                    array_push($docutype, "b_eid");
                  }
                }
              }
            }
            if(!$valid)
            {
              $point = "Location: ../error/?err=E100002";
              if(isset($_GET['redirect_uri_fail']))
              {
                $point .= "&redir_uri=";
                $point .= $_GET['redirect_uri_fail'];
              }
              else
              {
                if(isset($_GET['redirect_uri']))
                {
                  $point .= "&redir_uri=";
                  $point .= $_GET['redirect_uri'];
                }
              }
              header($point);
              die();
            }
            $clientid = $_GET['client_id'];
            $valid = false;
            try
            {
              require_once "../includes/dbh.inc.php";
              $query = "SELECT Domain FROM sp_addresses WHERE FK_ClientID = :clientid;";
              $stmt = $pdo->prepare($query);
              $stmt->bindParam(":clientid", $clientid);
              $stmt->execute();
              $domains = $stmt->fetchAll(PDO::FETCH_ASSOC);

              //$pdo = null;
              $stmt = null;
              
              $redirect_uri =  urldecode($_GET['redirect_uri']);
              $host = parse_url($redirect_uri, PHP_URL_HOST);

              $redirect_uri_f =  urldecode($redir_fail);
              $host_f = parse_url($redirect_uri_f, PHP_URL_HOST);
              //var_dump($domains);
              if(sizeof($domains)<1)
              {
                $point = "Location: ../error/?err=E100004&redir_uri=" . $redir_fail;
                header($point);
                die();
              }
              foreach($domains as $domain)
              {
                if(gethostbyname($domain['Domain'])===gethostbyname($host) && gethostbyname($domain['Domain'])===gethostbyname($host_f))
                {
                    $valid = true;
                    break;
                }
                
              }
            }
            catch(PDOException $e)
            {
              $point = "Location: ../error/?err=E000001&redir_uri=" . $redir_fail;
              header($point);
              die();
            }

            if(!$valid)
            {
              $point = "Location: ../error/?err=E100003&redir_uri=" . $redir_fail;
              header($point);
              die();
            }
            
            /*if(gethostbyname('localhost')!==gethostbyname($host)) //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!De inlocuit "localhost" cu adresa declarata!!!!
            {
              //echo gethostbyname($redirect_uri);
              $point = "Location: ../error/?err=E100003&redir_uri=" . $redir_fail;
              header($point);
              die();
            }
            
            if(gethostbyname('localhost')!==gethostbyname($host))
            {
              //echo gethostbyname($redirect_uri);
              $point = "Location: ../error/?err=E100003&redir_uri=" . $redir_fail;
              header($point);
              die();
            }*/
            //session_name("session_PMS");
            //session_start();
              //if(isset($_SESSION['preferred_username'])){
                //echo "<span class=\"d-flex\">
                      //  <a href=\"logout/\"><button class=\"btn border border-black border-2 btn-light\" style=\"font-weight:600;\" type=\"submit\"><img class=\"me-1\" src=\"connect.png\" height=\"24\">" . $_SESSION['preferred_username'] . "</button></a>
                    //  </span>";
             // }
             // else
             // {
             //   session_destroy();
              //echo "<span class=\"d-flex\">
             //           <a href=\"login/\"><button class=\"btn border border-black border-2 btn-light\" style=\"font-weight:600;\" type=\"submit\"><img class=\"me-1\" src=\"connect.png\" height=\"24\">Conectare</button></a>
              //      </span>";
             // }
            ?>
    <nav class="navbar sticky-top navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid shadow-sm py-3 px-5 mb-2 bg-body-tertiary rounded">
            <a class="navbar-brand py-3 me-4" style="font-weight: 700; margin-inline-start: 10%;" href="#">
                <img src="../resources/connect.png" alt="Logo" height="32" class="d-inline-block align-text-middle">
                <span style="font-weight: 500;">Ro-</span>Connect
            </a>
            
            <!--<span class="d-flex">
                <button class="btn border border-black border-2 btn-light" style="font-weight:600;" type="submit"><img class="me-1" src="connect.png" height="24">Conectare</button>
            </span>-->
            <!--<button class="navbar-toggler border-0" onclick="this.blur();" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">+</button>-->
        </div>
    </nav>

    <style>
        a .login-card{
            text-decoration: none !important;
        }
        div .login-id:hover {
            box-shadow: 0 6px 30px 0 rgba(0, 0, 0, 0.1), 0 10px 50px 0 rgba(0, 0, 0, 0.1) !important;
            transition: .2s !important;
        }
        div .login-id {
            transition: .2s !important;
        }

        div .login-id.inactive {
            opacity:40% !important;
            box-shadow:0 6px 30px 0 rgba(0, 0, 0, 0.1), 0 10px 50px 0 rgba(0, 0, 0, 0.1) !important;
        }

        div .login-id.inactive:hover {
            box-shadow:0 6px 30px 0 rgba(0, 0, 0, 0.1), 0 10px 50px 0 rgba(0, 0, 0, 0.1) !important;
        }
        </style>
    <div class="mx-5 mt-3">
        <div class="d-flex flex-row align-items-stretch flex-wrap justify-content-evenly mx-5 mb-3">
            <?php
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
            $login_required = true;
            if(isset($_SESSION['sub'])){
              if(isset($_SESSION['eidas_level']) && $_SESSION['eidas_level'] >= $accred )
              {
                $login_required = false;
                if(sizeof($docutype)>0 && (!isset($_SESSION['document']) || !in_array($_SESSION['document'],$docutype)))
                {
                  $login_required = true;
                }
                if($pers!=0 && (!isset($_SESSION['pers']) || $_SESSION['pers']!=$pers))
                {
                  $login_required = true;
                }
              }
            }
            //$login_required = false; //!!!!!!!!!!!!!!!!!!!!!! De comentat dupa lucrul la pagina "Logged in"!!!
            if(!$login_required){
              include "../includes/loggedin.php";
            }
            else
            {
              session_unset();
              session_destroy();
              include "../includes/idp_select.php";
            }
              
            ?>
            <!--<div class="p-2 align-self-center">
                <a class="login-card" href="#">
                    <div class="login-id card border-0 shadow" style="width: 18rem;">
                        <img src="../resources/ro_id.png" class="py-5 px-5 card-img-top" alt="Ro-ID">-->
                        <!--<div class="text-center card-body">
                            <p style="text-decoration: none !important;" class="fs-4 card-text">Ro-ID</p>
                        </div>-->
                    <!--</div>
                </a>
            </div>-->
            <!--<div class="p-2 align-self-center">
                <a class="login-card">
                    <div data-toggle="tooltip" data-placement="top" title="Acest cont nu este acceptat de către furnizorul de servicii." class="login-id inactive card border-0 shadow" style="width: 18rem;">
                        <img src="../resources/edu_id.png" class="py-5 px-5 card-img-top" alt="Edu-ID">
                    </div>
                </a>
            </div>
            <div class="p-2 align-self-center">
                <a class="login-card" href="#">
                    <div class="login-id card border-0 shadow" style="width: 18rem;">
                        <img src="../resources/business_id.png" class="py-5 px-5 card-img-top" alt="Business-ID">
                    </div>
                </a>
            </div>-->
        </div>
    </div>
    
    <?php
        //echo "Hello World!";
    ?>

    <div class="footer container">
    <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
        <p class="col-md-4 mb-0 text-body-secondary">© 2023</p>

        <a href="#" class="col-md-3 d-flex align-items-center justify-content-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
        <img class="bi me-2" src="../resources/connect.png" height="32"></img>
        </a>

        <ul class="nav col-md-5 justify-content-end">
        <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Despre</a></li>
        <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Protecția datelor</a></li>
        <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Întrebări</a></li>
        <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Date tehnice</a></li>
        </ul>
    </footer>
    </div>
    <script>
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();
});
</script>
    
</body>
</html>