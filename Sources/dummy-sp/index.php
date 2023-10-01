<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrație locală</title>
    <link rel="icon" type="image/x-icon" href="favicon.png">
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
            <a class="navbar-brand me-4" style="font-weight: 700;" href="#">
                <img src="coa.png" alt="Logo" height="48" class="d-inline-block align-text-middle me-3">
                Administrație locală
            </a>
            
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <!--<a class="nav-link active" aria-current="page" href="#">Home</a>-->
                    <a class="nav-link mx-2" href="dashboard/">Taxe</a>
                    <span class="link-secondary py-2 px-2 d-none d-lg-block">|</span>
                    <a class="nav-link mx-2" href="dashboard/">Documente</a>
                    <span class="link-secondary py-2 px-2 d-none d-lg-block">|</span>
                    <a class="nav-link mx-2" href="#">Informații</a>
                    <!--<a class="nav-link disabled" aria-disabled="true">Disabled</a>-->
                </div>
            </div>
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
        
            session_name("session_PMS");
            session_start();
              if(isset($_SESSION['preferred_username'])){
                echo "<span class=\"d-flex\">
                        <a href=\"logout/\"><button class=\"btn border border-black border-2 btn-light\" style=\"font-weight:600;\" type=\"submit\"><img class=\"me-1\" src=\"connect.png\" height=\"24\">" . $_SESSION['preferred_username'] . "</button></a>
                      </span>";
              }
              else
              {
                session_destroy();
              echo "<span class=\"d-flex\">
                        <a href=\"login/\"><button class=\"btn border border-black border-2 btn-light\" style=\"font-weight:600;\" type=\"submit\"><img class=\"me-1\" src=\"connect.png\" height=\"24\">Conectare</button></a>
                    </span>";
              }
            ?>
            <!--<span class="d-flex">
                <button class="btn border border-black border-2 btn-light" style="font-weight:600;" type="submit"><img class="me-1" src="connect.png" height="24">Conectare</button>
            </span>-->
            <!--<button class="navbar-toggler border-0" onclick="this.blur();" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">+</button>-->
        </div>
    </nav>

    <div class="container text-center">
        <div class="row align-items-start">
            <div class="col-12 col-xxl-6">
                <div class="d-flex flex-column flex-md-row px-4 py-4 gap-4 md-5 align-items-center justify-content-center">
                    <div class="w-100">
                    <h2 class="pb-2 mb-3 border-bottom">Acțiuni</h2>
                    <div class="list-group">
                      
                        <a href="dashboard/" class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                        <img src="misc1.png" alt="twbs" width="32" height="32" class="flex-shrink-0">
                        <div class="d-flex gap-2 w-100 justify-content-between">
                            <div class="text-start">
                            <h6 class="mb-0">Plată impozite</h6>
                            <p class="mb-0 opacity-75">Persoane fizice și juridice</p>
                            </div>
                            <small class="opacity-50 text-success text-nowrap">●</small>
                        </div>
                        </a>
                        <a href="appointments/index.php?scop=ci" class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                        <img src="misc6.png" alt="twbs" width="32" height="32" class="flex-shrink-0">
                        <div class="d-flex gap-2 w-100 justify-content-between">
                            <div class="text-start">
                            <h6 class="mb-0">Reînnoire carte de identitate</h6>
                            <p class="mb-0 opacity-75">Evidența persoanelor</p>
                            </div>
                            <small class="opacity-50 text-primary text-nowrap">●</small>
                        </div>
                        </a>
                        <a href="appointments/index.php?scop=inn" class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                        <img src="misc5.png" alt="twbs" width="32" height="32" class="rounded-circle flex-shrink-0">
                        <div class="d-flex gap-2 w-100 justify-content-between">
                            <div class="text-start">
                            <h6 class="mb-0">Înregistrare nou-născut</h6>
                            <p class="mb-0 opacity-75">Programare la Oficiul Stării Civile</p>
                            </div>
                            <small class="opacity-50 text-primary text-nowrap">●</small>
                        </div>
                        </a>
                        <a href="appointments/index.php?scop=c" class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                        <!--<img src="https://github.com/twbs.png" alt="twbs" width="32" height="32" class="rounded-circle flex-shrink-0">-->
                        <img src="misc9.png" alt="twbs" width="32" height="32" class="flex-shrink-0">
                        <div class="d-flex gap-2 w-100 justify-content-between">
                            <div class="text-start">
                            <h6 class="mb-0">Căsătorii</h6>
                            <p class="mb-0 opacity-75">Programare la Oficiul Stării Civile</p>
                            </div>
                            <small class="opacity-50 text-primary text-nowrap">●</small>
                        </div>
                        </a>
                        <a href="appointments/index.php?scop=ir" class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                        <img src="misc8.png" alt="twbs" width="32" height="32" class="rounded-circle flex-shrink-0">
                        <div class="d-flex gap-2 w-100 justify-content-between">
                            <div class="text-start">
                            <h6 class="mb-0">Înmatriculări și radieri</h6>
                            <p class="mb-0 opacity-75">Programare online</p>
                            </div>
                            <small class="opacity-50 text-warning text-nowrap">●</small>
                        </div>
                        </a>
                        <a href="dashboard/" class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                        <img src="misc2.png" alt="twbs" width="32" height="32" class="rounded-circle flex-shrink-0">
                        <div class="d-flex gap-2 w-100 justify-content-between">
                            <div class="text-start">
                            <h6 class="mb-0">Alte operațiuni</h6>
                            <p class="mb-0 opacity-75">Transcrieri documente, programări SPADPP</p>
                            </div>
                            <small class="opacity-50 text-warning text-nowrap">●</small>
                        </div>
                        </a>
                    </div>
                    </div>
                </div>
                <h2 class="pb-2 pt-4 border-bottom">Altceva</h2>
                <form class="mt-4 d-flex flex-row mb-3">
                  <input class="p-2 me-1 form-control form-control-lg rounded border-0 shadow" type="text" placeholder="Alte servicii" aria-label=".form-control-lg example">
                  <button type="submit" class="me-1 btn btn-light">Căutare</button>
                </form>
            </div>
            <div class="col">
            <div class="container py-4 px-5" id="custom-cards">
              <h2 class="pb-2 border-bottom">Noutăți</h2>

              <div class="col col-rows-1 col-rows-lg-3 align-items-stretch g-2 px-5">
                <div class="row">
                  <div class="card my-3 card-cover w-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-color: #01192e; background-image: url('https://www.turnulsfatului.ro/uploads/images/2023/9/19/rectorat-ulbs-copy-l64a.jpg'); background-repeat: no-repeat; background-size: cover; background-blend-mode: overlay;">
                    <div class="d-flex flex-column w-100 p-5 pb-3 text-white text-shadow-1">
                      <h3 class="pt-5 mt-5 mb-4 display-6 lh-1 fw-bold">Toamna studențească la Sibiu</h3>
                      <ul class="d-flex list-unstyled mt-auto">
                        <li class="me-auto">
                          <img src="misc1.png" alt="Bootstrap" width="32" height="32" class="rounded-circle text-bg-dark border border-secondary shadow">
                        </li>
                        <li class="d-flex align-items-center me-3">
                          <svg class="bi me-2" width="1em" height="1em"><use xlink:href="#geo-fill"></use></svg>
                          <small>Sibiu</small>
                        </li>
                        <li class="d-flex align-items-center">
                          <svg class="bi me-2" width="1em" height="1em"><use xlink:href="#calendar3"></use></svg>
                          <small>2 oct.</small>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="card my-3 card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-color: #01192e; background-image: url('https://www.astrafilm.ro/images/Astra_Film_Festival_foto_AFF-08241127.jpeg?format=webp&width=1400'); background-repeat: no-repeat; background-size: cover; background-blend-mode: overlay;">
                    <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                      <h3 class="pt-5 mt-5 mb-4 display-6 lh-1 fw-bold">Astra Film Festival 2023</h3>
                      <ul class="d-flex list-unstyled mt-auto">
                        <li class="me-auto">
                          <img src="misc7.png" alt="Bootstrap" width="32" height="32" class="rounded-circle text-bg-dark border border-secondary shadow">
                        </li>
                        <li class="d-flex align-items-center me-3">
                          <svg class="bi me-2" width="1em" height="1em"><use xlink:href="#geo-fill"></use></svg>
                          <small>Piața Mare</small>
                        </li>
                        <li class="d-flex align-items-center">
                          <svg class="bi me-2" width="1em" height="1em"><use xlink:href="#calendar3"></use></svg>
                          <small>3 oct.</small>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>
        </div>
    </div>
    
    <?php
    if(isset($_GET['autherror']) && $_GET['autherror']==true)
    {
      echo '<div class="modal fade" id="errModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Eroare la autentificare</h1>
            <button type="button" class="btn-close btn-" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>A survenit o eroare la autentificare.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Închideți</button>
          </div>
        </div>
      </div>
    </div>
    <button type="button" id="mdltoggle" class="btn btn-primary" data-bs-toggle="modal" hidden data-bs-target="#errModal"></button>
    <script>
      window.onload = function () {
        document.getElementById("mdltoggle").click(); };
    </script>';
    }
    
    ?>

    <div class="container">
  <footer class="row row-cols-1 row-cols-sm-2 row-cols-md-5 py-5 my-5 border-top">
    <div class="col mb-3">
      <a href="#" class="d-flex align-items-center mb-3 link-body-emphasis text-decoration-none">
        <!--<svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"></use></svg>-->
        <img class="bi me-2" src="coa.png" height="32"></img>
      </a>
      <p class="text-body-secondary">© 2023</p>
    </div>

    <div class="col mb-3">

    </div>

    <div class="col mb-3">
      <h5>Contact</h5>
      <ul class="nav flex-column">
        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">Audiențe</a></li>
        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">Presă</a></li>
        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">Petiții</a></li>
        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">Corespeondență oficială</a></li>
      </ul>
    </div>

    <div class="col mb-3">
      <h5>Orașul Sibiu</h5>
      <ul class="nav flex-column">
        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">Patrimoniu</a></li>
        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">Cultură</a></li>
        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">Turism</a></li>
        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">Servicii publice</a></li>
      </ul>
    </div>

    <div class="col mb-3">
      <h5>Alte informații</h5>
      <ul class="nav flex-column">
        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">Protecția datelor</a></li>
        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">Primărie</a></li>
        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">Consiliu local</a></li>
      </ul>
    </div>
  </footer>
</div>

    
</body>
</html>