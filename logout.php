<?php

session_start();


$_SESSION = array();


if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}


session_destroy();


echo '<!DOCTYPE html>
<html>
<head>
    <script>
        alert("Logged out successfully!");
        window.location.href = "login.html";
    </script>
</head>
<body>
    <p>Logging out...</p>
</body>
</html>';
?>