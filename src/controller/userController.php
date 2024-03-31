<?php

    session_start();

    
    function changePassword() {
        
    }

    function getUserData($connect) {

        try {
            $userId = $_SESSION['userId'];

            $stmt = $connect->prepare("SELECT uc.*, ui.* FROM user_cred uc
                JOIN user_info ui ON uc.userID = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();

            $result = $stmt->get_result()->fetch_assoc();
                        
            return array("title" => "Success", "message" => "Action succeeded", "data" => $result);
            

        } catch (\Throwable $th) {            
            throw $th;
            return array("title" => "Internal Error", "message" => "Something has went wrong. Please try again later.", "data" => []);
        }
    }


?>