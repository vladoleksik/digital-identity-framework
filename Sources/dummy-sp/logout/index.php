<?php
$redirect_pos = "https://dummy-sp.localhost/";
if(isset($_GET["red_pos"]))
{
    $redirect_pos .= $_GET["red_pos"];
    if(isset($_GET["scop"]))
    {
        $redirect_pos .= "/index.php?scop=";
        $redirect_pos .= $_GET["scop"];
    }
}ini_set('session.session_use_only_cookies',1);
ini_set('session.use_strict_mode',1);

session_set_cookie_params([
    'lifetime' => 1800,
    'domain' => 'localhost',
    'path' => '/dummy-sp/',
    'secure' => true,
    'httponly' => true
]);
session_name("session_PMS");
session_start();

$url = "http://roconnect.localhost/logout?state=" . $_SESSION['state'] . "&post_logout_redirect_uri=" . urlencode($redirect_pos);

session_unset();
session_destroy();


session_name("auth_PMS");
session_start();
session_unset();
session_destroy();

header("Location: " . $url);
die();
?>