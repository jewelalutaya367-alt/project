<?php
session_start(); // Simulan ang session

// I-clear ang lahat ng session variables
$_SESSION = array();

// Kung ginagamit ang session cookie, burahin din ito para tuluyang malinis
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// I-destroy o tapusin ang session
session_destroy();

// I-redirect pabalik sa login page (palitan ang 'login.php' kung iba ang pangalan ng login file mo)
header("location: login.php");
exit;
?>