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
if(!isset($_SESSION['sub']))
{
    header('Location: ../error?err=E000000');
    die();
}

?>

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
    <nav class="navbar sticky-top navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid shadow-sm py-3 px-5 mb-2 bg-body-tertiary rounded">
            <a class="navbar-brand py-3 me-4" style="font-weight: 700; margin-inline-start: 10%;" href="#">
                <img src="../resources/connect.png" alt="Logo" height="32" class="d-inline-block align-text-middle">
                <span style="font-weight: 500;">Ro-</span>Connect
            </a>
        </div>
    </nav>

    <div class="mx-5 mt-3">
        <div class="text-center mx-3 py-5 px-2 login-card shadow rounded">
            <div class="px-5">
                <h2 class="mb-3" style="font-weight: 600;">Istoricul conectărilor dvs.</h2>
                <table class="table">
                <thead>
                    <tr>
                    <th scope="col">Dată și oră</th>
                    <th scope="col">Tip</th>
                    <th scope="col">Utilizator</th>
                    <th scope="col">Andresă IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        require_once "../includes/dbh.inc.php";
                        $query = "SELECT * FROM history WHERE sub = :sub;";
                        $stmt = $pdo->prepare($query);
                        $stmt->bindParam(":sub", $_SESSION['sub']);
                        $stmt->execute();
                        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
          
                        $pdo = null;
                        $stmt = null;

                        foreach($logs as &$log)
                        {
                            echo '<tr>
                            <th scope="row">' . $log['timp'] . '</th>
                            <td>Login Ro-Connect</td>
                            <td>Dvs.</td>
                            <td>' . $log['ip'] . '</td>
                        </tr>';
                        }

                        if(sizeof($logs)==0)
                        {
                            echo '<h3>Nu s-au găsit înregistrări.</h3>';
                        }
                    ?>
                </tbody>
                </table>
            </div>
        </div>
    </div>

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
