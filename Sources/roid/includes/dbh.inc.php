<?php
    $dsn = "mysql:host=localhost;dbname=roid";

    $dbusername = "root";
    $dbpassword = "";

    try
    {
        $pdo = new PDO($dsn, $dbusername, $dbpassword);
        $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
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