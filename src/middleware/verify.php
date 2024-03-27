<?php
    
    session_start();
    
    function verify($connect, $authToken, $username) {
        
        $token = $_COOKIE['token'];

        if (!isset($token)) { return array("title" => "Session Expired", "message" => "Please Log-in again!", "data" => false); }
                
        $stmt = $connect->prepare("SELECT COUNT(*) AS count FROM user_creds WHERE authToken = ? AND username = ?");
        $stmt->bind_param("ss", $authToken, $username);
        $stmt->execute();

        $result = $stmt->get_result();

        $count = $result->fetch_assoc()['count'];

        if ($count < 0) { return array("title" => "Failed", "message" => "User does not exists!", "data" => false); }
        
        return array("title" => "Success", "message" => "User exists!", "data" => true);
        
    }
 

?>