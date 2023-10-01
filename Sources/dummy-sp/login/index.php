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
}
ini_set('session.session_use_only_cookies',1);
ini_set('session.use_strict_mode',1);

session_set_cookie_params([
    'lifetime' => 1800,
    'domain' => 'dummy-sp.localhost',
    'path' => '/',
    'secure' => true,
    'httponly' => true
]);

session_name("auth_PMS");
session_start();
session_regenerate_id(true);

$state = 's' . bin2hex(random_bytes(32));
$nonce = 'n' . bin2hex(random_bytes(32));

$_SESSION['state'] = $state;
$_SESSION['nonce'] = $nonce;
$_SESSION['red_pos'] = $redirect_pos;

/*$_SESSION['preferred_username'] = "Vlad-Andrei Oleksik";
$_SESSION['given_name'] = "Vlad-Andrei";
$_SESSION['family_name'] = "Oleksik";
$_SESSION['cnp'] = "5041111324780";

//optionale
$_SESSION['id_type'] = "CI";
$_SESSION['id_batch'] = "SR";
$_SESSION['id_sn'] = "043645";
$_SESSION['email'] = "vlad_oleksik@yahoo.com";
$_SESSION['sub'] = "8e3d794ed27fb9c164ca5a65040c0e952cd41a32ef0da255f80432ee2183c796";*/

header("Location: https://roconnect.localhost/authorize/?scope=openid%20given_name%20family_name%20cnp%20preferred_username%20birthdate%20gender%20serie_id&redirect_uri=https%3A%2F%2Fdummy-sp.localhost%2Foidc_callback&redirect_uri_err=https%3A%2F%2Fdummy-sp.localhost%2F%3Fautherror%3Dtrue&response_type=code&client_id=804d3b959d1e9e9c88f3e0c3e3af2e89247a1ef46c5d4ba251dab12897e8862b&state=" . 
   $state . "&nonce=" . $nonce . "&acr_values=eidas1&id-types=eid");
//header("Location: " . $redirect_pos);
die();
?>