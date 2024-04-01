<?php

    

    function generateTrackingNumber($name) {
        $timestamp = time(); // Current timestamp
        $randomNumbers = mt_rand(1000, 9999); // Random 4-digit number
        $randomLetters = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4); // Random 4 letters
        $recipient = substr($name, 0, 4); // Extract first 4 letters of recipient's name
        
        $trackingNumber = "TN-" . $recipient . "-" . $randomLetters . "-" . $timestamp . "-" . $randomNumbers;
        
        return $trackingNumber;
    }
    
    // returns the initial location of the documents that will be requested
    function routingProcess($documentType, $department) {
        // Read JSON data from file
        $routingProcess = json_decode(file_get_contents('../docs process/routing process.json'), true);
    
        // Check if the department exists in the JSON data
        if (isset($routingProcess[$department])) {
            // Get the array associated with the provided department and document type
            $documentArray = $routingProcess[$department];

            // Check if the document type exists in the array
            if (isset($documentArray[$documentType])) {
                // Split the routing process string by comma and return the first part
                $processParts = explode(',', $documentArray[$documentType]);
                return trim($processParts[0], "[]"); // Remove square brackets and trim whitespace
            }
        }
        // Return a default string if no match is found
        return null;
    }
    
    function releaseDocument($connect, $data) {
        
        try {
            $accessLevel = $_SESSION['accessLevel'];
            $requestedDocId = $data['requestedDocId'];
            $status = "Released";
            if ($accessLevel <  1) {
                return array("title" => "Failed", "message" => "Unauthorized User", "data" => []);
            } 

            $stmt = $connect->prepare("UPDATE request_documents 
                SET status = ?
                WHERE requestId = ?");
            
            $stmt->bind_param("ss", $status, $requestedDocId);
            $stmt->execute();
            
            if ($stmt->affected_rows !== 1) {
                return array("title" => "Failed", "message" => "Request ID Invalid.", "data" => []);
            }

            return array("title" => "Success", "message" => "Released Document", "data" => []);

        } catch (\Throwable $th) {
            throw $th;
            return array("title" => "Internal Error", "message" => "Something has went wrong. Please try again later.", "data" => []);
        }         
    }  

    function updateRequestedDoc($connect, $data) {
        try {
            $remarks = $data['remarks'];
            $releaseDate = $data['releaseDate'];
            $location = $data['location'];
            $status = $data['status'];
            $requestId = $data['requestId'];
            
            $stmt = $connect->prepare("UPDATE request_documents 
                SET remarks = ?, releaseDate = ?, location = ?, status = ?
                WHERE requestId = ?");
            $stmt->bind_param("ssssi", $remarks, $releaseDate, $location, $status, $requestId);
            $stmt->execute();

            return array("title" => "Success", "message" => "Action Succeeded", "data" => []);
        } catch (\Throwable $th) {
            throw $th;
            return array("title" => "Internal Error", "message" => "Something has went wrong. Please try again later.", "data" => []);
        }    
    }
    // updates the location of the requested doc
    function updateLocRequestedDoc($connect, $data) {
        try {
            $location = $data['location'];
            $requestId = $data['requestId'];
            $stmt = $connect->prepare("UPDATE request_documents SET location = ? WHERE requestId = ?");
            $stmt->bind_param("si", $location, $requestId);
            $stmt->execute();

            return array("title" => "Success", "message" => "Action Succeeded", "data" => []);

        } catch (\Throwable $th) {
            throw $th;
            return array("title" => "Internal Error", "message" => "Something has went wrong. Please try again later.", "data" => []);
        }
    }

    function searchTrackNumber($connect, $data) {

        try {
            $trackingNumber = $data['trackingNumber'];

            $stmt = $connect->prepare("SELECT rd.*, d.*
                FROM request_documents rd
                JOIN documents d ON rd.documentId = d.documentId
                WHERE rd.trackingNumber = ? ");
            $stmt->bind_param("s", $trackingNumber);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows < 0) {
                return array("title" => "Success", "message" => "No record found!", "data" => []);
            }

            $row = $result->fetch_assoc();
            
            $data = array(
                'requestId,' => $row['requestId'],
                'userId,' => $row['userId'],
                'documentId' => $row['documentId'],
                "trackingNumber" => $row['trackingNumber'],
                "remarks" => $row['remarks'],
                "releaseDate" => $row['releaseDate'],
                "location" => $row['location'],
                "status" => $row['status'],
                "createdAt" => $row['createdAt'],
                "recipient" => $row['recipient'],
                "department" => $row['department'],
                "documentType" => $row['documentType'],
                "note" => $row['note'],
                "purpose" => $row['purpose'],
                "actionsNeeded" => $row['actionsNeeded'],
                "documentTitle" => isset($row['documentTitle']) ? $row['documentTitle'] : "None",
                "documentFile" => isset($row['documentFile']) ? $row['documentFile'] : "No File"
            );

            return array("title" => "Success", "message" => "Record found!", "data" => $data);
            

        } catch (\Throwable $th) {
            throw $th;
            return array("title" => "Internal Error", "message" => "Something has went wrong. Please try again later.", "data" => []);
        }

    }
    
    function requestDocument($connect, $data) {

        $connect->begin_transaction();

        try {
            $userId = $_SESSION['userId'];
            $recipient = $data['recipient'];
            $trackingNumber = generateTrackingNumber($recipient);
            $toDepartment = $data['department'];
            $documentType = $data['documentType'];
            $note = $data['note'];
            $purpose = $data['purpose'];
            $actionsNeeded = $data['actionsNeeded'];
            $location = routingProcess($documentType, $toDepartment);

            if (!$location) {
                return array("title" => "Failed", "message" => "Location is invalid", "data" => []);
            }

            $documentTitle = $data['documentTitle'];
            $documentFile = $data['documentFile'];
                                
            $stmtDocument = $connect->prepare("INSERT INTO documents (title, document, recipient, 
                department, documentType, note, 
                purpose, actionsNeeded) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?) 
            ");

            $stmtDocument->bind_param("ssssssss", $documentTitle, $documentFile, $recipient, 
                $toDepartment, $documentType, $note, $purpose, $actionsNeeded);
            $stmtDocument->execute();
            $documentId = $stmtDocument->insert_id;

            $status = 'Pending';
            $stmtRequest = $connect->prepare("INSERT INTO request_documents (userId, documentId, 
                trackingNumber, location, status) 
                VALUES (?, ?, ?, ?, ?) 
            ");            
            $stmtRequest->bind_param("iisss", $userId, $documentId, $trackingNumber, $location, $status);
            $stmtRequest->execute();

            $connect->commit();
            
            return array("title" => "Success", "message" => "Action succeeded", "data" => []);

            
        } catch (\Throwable $th) {
            $connect->rollback();            
            throw $th;
            return array("title" => "Internal Error", "message" => "Something has went wrong. Please try again later.", "data" => []);
        }
        
    }

    function getAllDocuments($connect) {

        try {
            $accessLevel = $_SESSION['accessLevel'];

            if ($accessLevel === '1') {
                return array("title" => "Failed", "message" => "Not Authorized!", "data" => []);
            }
            $stmt = null;
            if ($accessLevel === '3') {                
                $stmt = $connect->prepare("SELECT rd.*, d.*
                    FROM request_documents rd
                    JOIN documents d ON rd.documentId = d.documentId");

            } else if ($accessLevel === '2') {
                $userId = $_SESSION['userId'];
                $department = $_SESSION['department'];

                $stmt = $connect->prepare("SELECT rd.*, d.*
                    FROM request_documents rd
                    JOIN documents d ON rd.documentId = d.documentId
                    WHERE rd.userId = ? AND department = ?
                ");
                $stmt->bind_param("is", $userId, $department);                
            }

            $stmt->execute();
            $result = $stmt->get_result();
                        
            $data = array();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                                    
                    $data[$row['documentId']] = array(
                        'requestId,' => $row['requestId'],
                        'userId,' => $row['userId'],
                        'documentId' => $row['documentId'],
                        "trackingNumber" => $row['trackingNumber'],
                        "remarks" => $row['remarks'],
                        "releaseDate" => $row['releaseDate'],
                        "location" => $row['location'],
                        "status" => $row['status'],
                        "createdAt" => $row['createdAt'],
                        "recipient" => $row['recipient'],
                        "department" => $row['department'],
                        "documentType" => $row['documentType'],
                        "note" => $row['note'],
                        "purpose" => $row['purpose'],
                        "actionsNeeded" => $row['actionsNeeded'],
                        "documentTitle" => isset($row['documentTitle']) ? $row['documentTitle'] : "None",
                        "documentFile" => isset($row['documentFile']) ? $row['documentFile'] : "No File"
                    );

                    return array("title" => "Success", "message" => "Action succeeded", "data" => $data);
                }
            }            
        } catch (\Throwable $th) {
            throw $th;
            return array("title" => "Internal Error", "message" => "Something has went wrong. Please try again later.", "data" => []);

        }
    }
    
    
?>