<?php

    session_start();

    
    function changePassword() {
        
    }

    function getUserData($connect) {

        try {
            $userId = $_SESSION['userId'];
            $username = $_SESSION['username'];

            $stmt = $connect->prepare("SELECT * FROM user_info WHERE id = ? AND username = ? ");
            $stmt->bind_param("is", $userId, $username);
            $stmt->execute();

            $result = $stmt->get_result()->fetch_assoc();
                        
            return array("title" => "Success", "message" => "Action succeeded", "data" => $result);
            

        } catch (\Throwable $th) {            
            throw $th;
            return array("title" => "Internal Error", "message" => "Something has went wrong. Please try again later.", "data" => []);
        }
    }


?>