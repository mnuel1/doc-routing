<?php
    
// Set the rate limit values
$limit = 100; // Max number of requests allowed
$window = 60; // Time window in seconds

// Get the client's IP address
$clientIP = $_SERVER['REMOTE_ADDR'];

// Generate a unique identifier for the current request based on the client's IP address
$requestKey = 'api_rate_limit_' . $clientIP;

// Check if the request key exists in the session
if (!isset($_SESSION[$requestKey])) {
    // If the request key doesn't exist, initialize it with an empty array
    $_SESSION[$requestKey] = array();
}

// Get the current timestamp
$currentTime = time();

// Remove expired timestamps from the session data
$_SESSION[$requestKey] = array_filter($_SESSION[$requestKey], function ($timestamp) use ($currentTime, $window) {
    return ($currentTime - $timestamp) <= $window;
});

// Count the number of requests made within the current window
$requestCount = count($_SESSION[$requestKey]);

// Check if the request count exceeds the limit
if ($requestCount >= $limit) {
    // If the limit is exceeded, return an error response
    http_response_code(429); // 429 Too Many Requests
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'Rate limit exceeded'));
    exit;
}

// If the limit is not exceeded, add the current timestamp to the session data
$_SESSION[$requestKey][] = $currentTime;


