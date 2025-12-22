<?php

session_start();


$conn = mysqli_connect("localhost", "root", "", "used_books");
if (!$conn) {
    die("Database connection failed");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        
        session_regenerate_id(true);
        
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['logged_in'] = true;
        
        
        setcookie(session_name(), session_id(), time() + 86400, '/', '', false, true);
        
       
        mysqli_close($conn);
        
       
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>Redirecting...</title>
            <script>
                alert("Login successful!");
                
                window.location.href = "http://' . $_SERVER['HTTP_HOST'] . '/projectcomp12B/marketplace.php";
            </script>
        </head>
        <body>
            <p>Redirecting to marketplace...</p>
            <p>If not redirected, <a href="marketplace.php">click here</a></p>
        </body>
        </html>';
        exit();
        
    } else {
        mysqli_close($conn);
        echo '<script>
            alert("Invalid email or password!");
            window.location.href = "login.html";
        </script>';
        exit();
    }
}


echo '<script>window.location.href = "login.html";</script>';
exit();
?>