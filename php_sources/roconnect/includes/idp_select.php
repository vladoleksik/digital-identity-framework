<?php
              $query = "SELECT ProviderID, ProviderTitle, ProviderImg, ProviderAddress, ProviderName FROM id_providers WHERE EidasLevel >= :eidas";
              if($pers!==0)
              {
                $query .= " AND PersonType = :pers";
              }
              if(sizeof($docutype)>0)
              {
                $i=0;
                $query .= " AND (DocumentUsed = :docu" . $i;
                for($i=1;$i<sizeof($docutype);$i++)
                {
                  $query .= " OR DocumentUsed = :docu" . $i;
                }
                $query .= ")";
              }
              $query .= ";";
              //echo $query;
              $stmt = $pdo->prepare($query);
              $stmt->bindParam(":eidas", $accred);
              if($pers!==0)
              {
                if($pers==1)
                {
                  $pname = "pf";
                  
                }
                if($pers==2)
                {
                  $pname = "pj";
                }
                $stmt->bindParam(":pers", $pname);
                
              }
              if(sizeof($docutype)>0)
              {
                for($i=0;$i<sizeof($docutype);$i++)
                {
                  //echo ":docu" . $i .": " . $docutype[$i] . "<br/>";
                  $stmt->bindParam(":docu" . $i, $docutype[$i]);
                }
              }
              $stmt->execute();
              $providers = $stmt->fetchAll(PDO::FETCH_ASSOC);
              
              //var_dump($providers);

              foreach($providers as &$provider)
              {
                echo "<div class=\"p-2 align-self-center\">
                          <a class=\"login-card\" href=\"../authenticate?response_type=code&idp_index=" . $provider['ProviderID'] . "&provider_name=" . $provider['ProviderName'] . "&provider_url=" . urlencode("https://" . $provider['ProviderAddress']) . "&client_id=412800e26513f4f0a7818a6ff1aa866ba2308bbbf607fa14716467238ca8c951&redirect_uri=https%3A%2F%2Froconnect.localhost%2Foidc_callback&getparams=" . urlencode(base64_encode(explode('?',$_SERVER['REQUEST_URI'])[1])) .  "&scope=" . $_GET['scope'] . "&acr_values=" . $_GET['acr_values'] . "\">
                              <div class=\"login-id card border-0 shadow\" style=\"width: 18rem;\">
                                  <img src=\"../resources/" . $provider['ProviderImg'] . "\" class=\"py-5 px-5 card-img-top\" alt=\"" . $provider['ProviderTitle'] . "\">
                              </div>
                          </a>
                      </div>";
              }


              //$pdo = null;
              $stmt = null;

              //second pass


              $query = "SELECT ProviderTitle, ProviderImg, ProviderAddress FROM id_providers WHERE NOT(EidasLevel >= :eidas";
              if($pers!==0)
              {
                $query .= " AND PersonType = :pers";
              }
              if(sizeof($docutype)>0)
              {
                $i=0;
                $query .= " AND (DocumentUsed = :docu" . $i;
                for($i=1;$i<sizeof($docutype);$i++)
                {
                  $query .= " OR DocumentUsed = :docu" . $i;
                }
                $query .= ")";
              }
              $query .= ");";
              //echo $query;
              $stmt = $pdo->prepare($query);
              $stmt->bindParam(":eidas", $accred);
              if($pers!==0)
              {
                if($pers==1)
                {
                  $pname = "pf";
                  
                }
                if($pers==2)
                {
                  $pname = "pj";
                }
                $stmt->bindParam(":pers", $pname);
                
              }
              if(sizeof($docutype)>0)
              {
                for($i=0;$i<sizeof($docutype);$i++)
                {
                  //echo ":docu" . $i .": " . $docutype[$i] . "<br/>";
                  $stmt->bindParam(":docu" . $i, $docutype[$i]);
                }
              }
              $stmt->execute();
              $providers = $stmt->fetchAll(PDO::FETCH_ASSOC);
              
              //var_dump($providers);

              foreach($providers as $provider)
              {
                echo "<div class=\"p-2 align-self-center\">
                          <a class=\"login-card\" href=\"" . $provider['ProviderAddress'] . "\">
                              <div data-toggle=\"tooltip\" data-placement=\"top\" title=\"Acest cont nu este acceptat de către furnizorul de servicii.\" class=\"login-id inactive card border-0 shadow\" style=\"width: 18rem;\">
                                  <img src=\"../resources/" . $provider['ProviderImg'] . "\" class=\"py-5 px-5 card-img-top\" alt=\"" . $provider['ProviderTitle'] . "\">
                              </div>
                          </a>
                      </div>";
              }


              $pdo = null;
              $stmt = null;
            ?>