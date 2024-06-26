<?php
    
    require_once '../../config/config.php';
    require_once '../controller/auth.php';


    if ($_SERVER['REQUEST_URI'] === "/login" 
        && $_SERVER['REQUEST_METHOD'] === 'POST') {
        
        include '../middleware/apiRateLimiter.php';
        
        // Check if username and password are set
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
                
            // Call login function
            $response = login($connect, $username, $password);

            echo json_encode($response);
        } else {
            // Output error message
            echo "Username and/or password are empty";
        }
    }


?>