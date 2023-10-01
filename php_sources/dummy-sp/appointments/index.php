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
          <a class="nav-link mx-2" href="../dashboard/">Taxe</a>
          <span class="link-secondary py-2 px-2 d-none d-lg-block">|</span>
          <a class="nav-link mx-2" href="../dashboard/">Documente</a>
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
          $red_pos = "Location: https://dummy-sp.localhost/login/index.php?red_pos=appointments";
          if(isset($_GET['scop']))
          {
            $red_pos .= "?scop=";
            $red_pos .= $_GET['scop'];
          }
          header($red_pos);
          die();
        }
      ?>
          <!--<span class="d-flex">
              <button class="btn border border-black border-2 btn-light" style="font-weight:600;" type="submit"><img class="me-1" src="connect.png" height="24">Conectare</button>
          </span>-->
          <!--<button class="navbar-toggler border-0" onclick="this.blur();" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">+</button>-->
    </div>
  </nav>

  <div class="mx-lg-5 mx-3 mt-5 px-5">
    <div class="mx-xl-5 mx-2 shadow p-3 rounded">
      <h2 class="pb-2 mb-3 border-bottom text-center">Efectuare programare</h2>
      <form id="FormAppointment">
        <div class="d-flex justify-content-around flex-wrap flex-row mb-3">
          <div class="p-2 flex-grow-1">
            <label for="FormAppointment1" class="form-label">Nume</label>
            <input id="FormAppointment1" class="form-control" type="text" value="<?php echo $_SESSION['family_name'];?>" aria-label="Disabled input example" disabled readonly>
          </div>
          <div class="p-2 flex-grow-1">
            <label for="FormAppointment2" class="form-label">Prenume</label>
            <input id="FormAppointment2" class="form-control" type="text" value="<?php echo $_SESSION['given_name'];?>" aria-label="Disabled input example" disabled readonly>
          </div>
          <div class="p-2 flex-fill">
            <label for="FormAppointment3" class="form-label">CNP</label>
            <input id="FormAppointment3" class="form-control" type="text" value="<?php echo $_SESSION['cnp'];?>" aria-label="Disabled input example" disabled readonly>
          </div>
          <div class="p-2 flex-shrink-1">
            <label for="FormAppointment4" class="form-label">Serie/Număr CI</label>
            <div class="input-group">
              <input id="FormAppointment4" class="form-control w-25" type="text" value="<?php echo $_SESSION['id_batch'];?>" aria-label="Disabled input example" disabled readonly>
              <input class="form-control w-75" type="text" value="<?php echo $_SESSION['id_sn'];?>" aria-label="Disabled input example" disabled readonly>
            </div>
          </div>
          <div class="p-2 flex-shrink-2">
            <label for="FormAppointment5" class="form-label">Dată și oră programare</label>
            <div class="input-group">
              <input id="FormAppointment5" class="form-control" type="date" value="2023-10-06" aria-label="Disabled input example">
              <input class="form-control" type="time" value="12:45" aria-label="Disabled input example">
            </div>
          </div>
          <div class="p-2 flex-fill">
            <label for="FormAppointment6" class="form-label">Locație</label>
            <input id="FormAppointment6" class="form-control" type="text" value="Piața Mare" aria-label="Disabled input example" disabled readonly>
          </div>
          <div class="p-2 flex-fill">
            <label for="FormAppointment7" class="form-label">Motiv programare</label>
            <select id="FormAppointment7" class="form-select" value="ci" aria-label="Default select example">
              <option <?php if(!isset($_GET['scop'])){echo "selected";}?> value="def">Alegeți din listă</option>
              <option <?php if(isset($_GET['scop']) && $_GET['scop']=='inn'){echo "selected";}?> value="inn">Înregistrare nou-născut</option>
              <option <?php if(isset($_GET['scop']) && $_GET['scop']=='ci'){echo "selected";}?> value="ci">Schimbare carte de identitate</option>
              <option <?php if(isset($_GET['scop']) && $_GET['scop']=='ir'){echo "selected";}?> value="ir">Înmatriculări/Radieri</option>
              <option <?php if(isset($_GET['scop']) && $_GET['scop']=='c'){echo "selected";}?> value="c">Căsătorie</option>
              <option <?php if(isset($_GET['scop']) && $_GET['scop']=='aud'){echo "selected";}?> value="c">Audiență</option>
            </select>
          </div>
        </div>
        <div class="text-center p-2 flex-fill">
          <button class="btn btn-primary" type="submit">Confirmare</button>
        </div>
      </form>
      
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