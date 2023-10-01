<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrație locală</title>
    <link rel="icon" type="image/x-icon" href="../favicon.png">
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
      <a class="navbar-brand me-4" style="font-weight: 700;" href="../">
        <img src="../coa.png" alt="Logo" height="48" class="d-inline-block align-text-middle me-3">
        Administrație locală
      </a>
          
      <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
          <!--<a class="nav-link active" aria-current="page" href="#">Home</a>-->
          <a class="nav-link mx-2" href="#">Taxe</a>
          <span class="link-secondary py-2 px-2 d-none d-lg-block">|</span>
          <a class="nav-link mx-2" href="#">Documente</a>
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
                  <a href=\"../logout/\"><button class=\"btn border border-black border-2 btn-light\" style=\"font-weight:600;\" type=\"submit\"><img class=\"me-1\" src=\"../connect.png\" height=\"24\">" . $_SESSION['preferred_username'] . "</button></a>
                </span>";
        }
        else
        {
          session_destroy();
          header("Location: https://dummy-sp.localhost/login/index.php?red_pos=dashboard");
          die();
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
        <h2 class="pb-2 pt-4 border-bottom">Taxe și impozite</h2>
        <div class="table-responsive mt-3">
          <table class="table align-middle table-hover">
            <thead class="table-light">
              <tr>
                <th scope="col">Dată</th>
                <th scope="col">Tip taxă</th>
                <th scope="col">Bun/Serviciu</th>
                <th scope="col">Sumă (lei)</th>
                <th scope="col">Stare</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if($_SESSION['cnp']=='5041111324780')
              {
                echo '<tr>
                        <td>17.11.2022</td>
                        <td>Impozit</td>
                        <td>Clădire</td>
                        <td>465.25</td>
                        <td><button class="border-0 btn-link bg-transparent text-primary">Neachitat</button></td>
                      </tr>
                      <tr>
                        <td>17.11.2022</td>
                        <td>Impozit</td>
                        <td>Teren</td>
                        <td>267.38</td>
                        <td>Achitat</td>
                      </tr>
                      <tr>
                        <td>17.11.2022</td>
                        <td>Impozit</td>
                        <td>Autovehicul</td>
                        <td>328.94</td>
                        <td>Achitat</td>
                      </tr>
                      <tr>
                        <td>13.09.2022</td>
                        <td>Taxă</td>
                        <td>Salubrizare</td>
                        <td>623.64</td>
                        <td>Achitat</td>
                      </tr>
                      <tr class="bt-2">
                        <th scope="col">Total de plată</th>
                        <td>-</td>
                        <td>-</td>
                        <th scope="col">465.25</th>
                        <td>-</td>
                      </tr>';
              }
              ?>
            </tbody>
          </table>
          <?php
           if($_SESSION['cnp']!='5041111324780')
           {
            echo '<h4>Nu există tranzacții recente.</h4>';
           }
          ?>
        </div>
        <?php if($_SESSION['cnp']=='5041111324780') echo '<button type="button" class="btn btn-primary">Achitare sume restante</button>'; ?>
        <div class="card mt-4">
          <div class="card-header">
            Alte operațiuni
          </div>
          <div class="card-body">
            <h5 class="card-title">Efectuați alte operațiuni</h5>
            <p class="card-text">Alegeți alte operațiuni din gama de demersuri realizabile online.</p>
            <a href="#" class="btn btn-primary">Căutare</a>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="container py-4 px-5" id="custom-cards">
          <h2 class="pb-2 border-bottom">Acțiuni</h2>

          <div class="col col-rows-1 col-rows-lg-3 align-items-stretch g-2 px-5">
            <div class="row">
              <div class="col-sm-6 mb-3 mb-sm-0">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Înnoire act de identitate</h5>
                    <p class="card-text">Programarea trebuie efectuată în perioada de valabilitate a vechiului act.</p>
                    <a href="../appointments/index.php?scop=ci" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-circle" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z"/>
                    </svg></a>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Programare audiență Primărie</h5>
                    <p class="card-text">Efectuați online o programare pentru o audiență la Primărie.</p>
                    <a href="../appointments/index.php?scop=aud" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-circle" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z"/>
                    </svg></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col col-rows-1 mt-5 col-rows-lg-3 align-items-stretch g-2 px-5">
            <div class="row">
              <div class="col-sm-6 mb-3 mb-sm-0">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Programare Stare Civilă</h5>
                    <p class="card-text">Programare online înregistrare nou-născut sau oficiere căsătorie.</p>
                    <a href="../appointments/index.php?scop=inn" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-circle" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z"/>
                    </svg></a>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Înmatriculare/ radiere</h5>
                    <p class="card-text">Programare pentru înmatricularea sau radierea unui autovehicul.</p>
                    <a href="../appointments/index.php?scop=ir" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-circle" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z"/>
                    </svg></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

    

    <?php
        //echo "Hello World!";
    ?>

    <div class="container">
  <footer class="row row-cols-1 row-cols-sm-2 row-cols-md-5 py-5 my-5 border-top">
    <div class="col mb-3">
      <a href="../" class="d-flex align-items-center mb-3 link-body-emphasis text-decoration-none">
        <!--<svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"></use></svg>-->
        <img class="bi me-2" src="../coa.png" height="32"></img>
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