

<div class="login-card text-center px-5 py-4 rounded shadow">
    <h5 class="mb-3" style="font-weight: 400;">V-ați conectat ca</h2>
    <h2 class="mb-3" style="font-weight: 600;"><?php echo $_SESSION['preferred_username']; ?></h2>
    <h5 class="mb-3 mt-5" style="font-weight: 400;">Acceptați comunicarea către <span style="font-weight: 600;"><?php
    $query = "SELECT SPTitle FROM service_providers WHERE ClientID = :clientid;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":clientid", $clientid);
    $stmt->execute();
    $sptitle = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pdo = null;
    $stmt = null;
   echo $sptitle[0]['SPTitle'];?></span> a următoarelor date personale?</h5>
    <div style="margin: auto !important;" class="text-start ps-lg-5 px-2 w-75">
    <?php
        $scopes = explode(' ',$_GET['scope']);
        foreach($scopes as &$scope)
        {
          if(!in_array($scope, array('openid', 'preferred_username', 'given_name',)))
          {
            echo '<h6 class="my-1 pt-2" style="font-weight: 500;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 bi bi-asterisk" viewBox="0 0 16 16">
            <path d="M8 0a1 1 0 0 1 1 1v5.268l4.562-2.634a1 1 0 1 1 1 1.732L10 8l4.562 2.634a1 1 0 1 1-1 1.732L9 9.732V15a1 1 0 1 1-2 0V9.732l-4.562 2.634a1 1 0 1 1-1-1.732L6 8 1.438 5.366a1 1 0 0 1 1-1.732L7 6.268V1a1 1 0 0 1 1-1z"/>
          </svg>';
            switch($scope)
            {
              case 'family_name':
                echo "Numele și prenumele";
                break;
              case 'birthdate':
                echo "Data nașterii";
                break;
              case 'cnp':
                echo "CNP-ul";
                break;
              case 'serie_id':
                echo "Seria și numărul buletinului";
                break;
              case 'gender':
                echo "Sexul";
                break;
              case 'email':
                echo "E-mailul";
                break;
              default:
                echo $scope;
            }
            echo '</h6>';
          }
        }
    ?>    
    </div>
    <div style="margin-inline: auto !important;" class="mt-3 flex-row w-75">
        <a href="<?php echo "got_consent.php?redirect_uri=" . $_GET['redirect_uri'] . "&state=" . $_GET['state'] . "&nonce=" . $_GET['nonce'] . "&client_id=" . $_GET['client_id']; ?>"><button class="w-100 my-2 btn text-start rounded-1 btn-primary" style="font-weight: 500;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-circle ms-lg-5 me-2" viewBox="0 0 16 16">
  <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0z"/>
  <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z"/>
</svg>Da</button></a>
        <a href="../logout?forced"><button class="w-100 my-2 btn text-start border-2 rounded-1 btn-outline-primary" style="font-weight: 500;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-left ms-lg-5 me-2" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0v2z"/>
  <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3z"/>
</svg>Nu (renunțare)</button></a>
    </div>
    
</div>