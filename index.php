<?php

    // Enable CORS (Cross-Origin Resource Sharing)
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    header("Access-Control-Allow-Credentials: true");

    // Set secure headers
    header("Content-Security-Policy: default-src 'self';");
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: DENY");
    header("X-XSS-Protection: 1; mode=block");
      
    require_once 'src/routes/route.php';
    require_once 'src/routes/authRoute.php';
    
?>