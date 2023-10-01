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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            <a class="navbar-brand py-3 me-4" style="font-weight: 700; margin-inline-start: 10%;" href="#">
                <img src="../resources/connect.png" alt="Logo" height="32" class="d-inline-block align-text-middle">
                <span style="font-weight: 500;">Ro-</span>Connect
            </a>
            <?php
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
        <div class="text-center mx-3 py-5 px-2 login-card shadow rounded">
            <div>
                <h2 class="mb-3" style="font-weight: 600;">Problemă de conectare</h2>
                <h6 class="my-3" style="font-weight: 500;">Tipul erorii: <?php if(isset($_GET['err'])) {echo $_GET['err'];}else{echo "E000000";} ?></h6>
                <h5 class="my-3 pt-3" style="font-weight: 400;"><?php
                if(isset($_GET['err']))
                {
                    switch($_GET['err'])
                    {
                        case "E000001":
                            echo "Eroare internă a Ro-Connect la procesarea solicitării de conectare.";
                            break;
                        case "E100000":
                            echo "Furnizorul de servicii nu a transmis date de conectare cu Ro-Connect.";
                            break;
                        case "E100001":
                            echo "Furnizorul de servicii nu a furnizat toți parametrii necesari pentru Ro-Connect.";
                            break;
                        case "E100002":
                            echo "Furnizorul de servicii a transmis date eronate către Ro-Connect.";
                            break;
                        case "E100003":
                            echo "Furnizorul de servicii a transmis o adresă de răspuns care nu îi aparține.";
                            break;
                        case "E100004":
                            echo "Furnizorul de servicii a transmis un ID eronat.";
                            break;
                        case "E200001":
                            echo "Furnizorul de identitate a transmis un răspuns incomplet.";
                            break;
                        case "E200002":
                            echo "Răspunsul furnizorului de identitate nu poate fi utilizat pentru autentificarea în siguranță.";
                            break;
                        case "E200003":
                            echo "Furnizorul de identitate selectat nu răspunde.";
                            break;
                        case "E200004":
                            echo "Răspunsul furnizorului de identitate nu respectă protocolul convenit.";
                            break;
                        case "E200005":
                            echo "Încercarea de autentificare a furnizorului de identitate folosește un cod de acces expirat.";
                            break;
                        case "E200006":
                            echo "Furnizorul de identitate nu a transmis toate datele personale cerute.";
                            break;
                        default:
                            echo "A survenit o eroare la autentificarea cu Ro-Connect.";
                    }
                }
                else
                {
                    echo "A survenit o eroare la autentificarea cu Ro-Connect.";
                }
                ?>
                </h5>
                <?php
                    if(isset($_GET['redir_uri']))
                    {
                        $redir_uri = $_GET['redir_uri'];
                        if(isset($_GET['args']))
                        {
                            $redir_uri .= "?";
                            $redir_uri .= $_GET['args'];
                        }
                        echo "<a href=\"" . $redir_uri . "\"><button class=\"mt-4 btn btn-primary\">Înapoi la furnizorul de servicii</button></a>";
                    }
                    else
                    {
                        echo "<button onclick=\"history.go(-2)\" class=\"mt-4 btn btn-primary\">Înapoi la furnizorul de servicii</button>";
                    }
                ?>
            </div>
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
    
</body>
</html>