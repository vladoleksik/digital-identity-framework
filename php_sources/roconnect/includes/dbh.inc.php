<?php
    $dsn = "mysql:host=localhost;dbname=roconnect";
    //$dbusername = "roconnect";
    //$dbpassword = "1(ntnxjl6(nyI(v!";

    $dbusername = "root";
    $dbpassword = "";

    /*echo "Hey! Generating a service provider...<br/>";
    $clientid = bin2hex(random_bytes(32));
    $clientsecret = bin2hex(random_bytes(32));
    $clientsecrethash = password_hash(hex2bin($clientsecret), PASSWORD_DEFAULT);
    $offuse = true;
    $shname = "Primarie";
    $presname = "Primaria Sibiu";
    echo $presname . "<br/>ClientID: " . $clientid . "<br/>ClientSecret: " . $clientsecret . "<br/>Hashed ClientSecret: " . $clientsecrethash;
    die(); //uncomment for it to work*/
    try
    {
        $pdo = new PDO($dsn, $dbusername, $dbpassword);
        $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        /*$query = "INSERT INTO service_providers (ClientID, ShortName, PresentationName, OfficialUse, ClientSecretHash) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$clientid,$shname,$presname,$offuse,$clientsecrethash]);
        $pdo = null;
        $stmt = null;*/
    }
    catch(PDOException $e)
    {
        if(isset($_GET['redirect_uri_fail']))
        {
            $point = "Location: ../error/?err=E000001&redir_uri=" . $_GET['redirect_uri_fail'];
        }
        else
        {
            if(isset($_GET['redirect_uri']))
            {
                $point = "Location: ../error/?err=E000001&redir_uri=" . $_GET['redirect_uri'];
            }
            else
            {
                $point = "Location: ../error/?err=E000001";
            }
        }
        header($point);
        die();
        //die("Query Failed: " . $e->getMessage());
    }
?>