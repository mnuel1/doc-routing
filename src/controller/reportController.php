<?php


    function getQuickSummaryReport($connect) {

        try {
            $accessLevel = $_SESSION['accessLevel'];
            if ($accessLevel < 1) {
                return array("title" => "Failed", "message" => "Unauthorized User.", "data" => []);
            }

            $stmt = $connect->prepare("SELECT status, COUNT(*) AS total_requests
                FROM request_documents GROUP BY status");
            $stmt->execute();
            $data = $stmt->get_result()->fetch_assoc();
            
            return array("title" => "Success", "message" => "Summary fetched.", "data" => $data);

            
        } catch (\Throwable $th) {
            throw $th;
            return array("title" => "Internal Error", "message" => "Something has went wrong. Please try again later.", "data" => []);
        }

    }

?>