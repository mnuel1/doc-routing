<?php
    
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/src/controller/auth.php';
  
    // if ($_SERVER['REQUEST_METHOD'] === 'POST' 
    //     && $_POST['route'] === '/login') {
        include '../middleware/apiRateLimiter.php';
        $_POST['username'] = 'test';
        $_POST['password'] = 'test';
        // Check if username and password are set
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
                
            // Call login function
            $response = login($connect, $username, $password);
            echo $_SESSION['accessLevel'];
            echo json_encode($response  );
        } else {
            // Output error message
            echo "Username and/or password are empty";
        }
    // }


?>