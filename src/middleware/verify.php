<?php
    
    session_start();
    
    function verify() {
        
        if (!isset($_COOKIE['token'])) { return array("title" => "Session Expired", "message" => "Please Log-in again!", "data" => false); }
        
        return array("title" => "Success", "message" => "User exists!", "data" => true);
        
    }
 

?>