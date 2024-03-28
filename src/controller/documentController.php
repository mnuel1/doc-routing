<?php

    session_start();

    function generateTrackingNumber($name) {
        $timestamp = time(); // Current timestamp
        $randomNumbers = mt_rand(1000, 9999); // Random 4-digit number
        $randomLetters = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4); // Random 4 letters
        $recipient = substr($name, 0, 4); // Extract first 4 letters of recipient's name
        

        $trackingNumber = "TN-" . $recipient . "-" . $randomLetters . "-" . $timestamp . "-" . $randomNumbers;
        
        return $trackingNumber;
    }
    // returns the initial location of the documents that will be requested
    /*
        ROUTING RULE BASED ON THE DOC TYPE
        HR TRAINING UNIT
        Documents for Direct Request:
        Training Attendance Records (Individual employee access only)
        Training Schedule and Calendar (Unless significant changes require department head approval)

        Documents Needing Centralized Approval:
        Trainee Employee Report (May require review by HR manager before sending to requesting department)
        Training Program Proposals (Requires approval by HR or training committee for budget allocation)
        Training Curriculum and Course Materials (May require review by subject matter experts before finalization)

        Finance Dept:

        Documents for Direct Request:
        Expense Reimbursement Policies and Procedures (Public facing document)
        Financial Statements (Public facing document, might require departmental review before release)

        Documents Needing Centralized Approval:
        Payroll (Highly sensitive, requires strict access controls)
        Budget Allocation Documents (Requires approval by finance manager or budget committee)
        Employee Benefits Cost Breakdown (Requires review by HR and finance for accuracy)

        Operations Management Dept:

        Documents for Direct Request:
        Shift Schedules and Rotations (Unless major changes require management approval)
        Job Descriptions and Specifications (Unless for new positions requiring HR review)
        
        Documents Needing Centralized Approval:
        Production Schedules (May require approval by production manager or executive team)
        Staffing Plans (Requires HR and Operations Management collaboration and approval)
        Workforce Planning Reports (May require review by senior management for strategic planning)
    */
    function routingRules($documentType) {

        return 'string';
    }
    function releaseDocument($connect, $data) {

    }
    // TODO ROUTING RULES, UPDATE RULES, RELEASE RULES ROUTES API
    function updateRequestedDoc($connect, $data) {

        try {
            $remarks = $data['remarks'];
            $releaseDate = $data['releaseDate'];
            $location = $data['location'];
            $status = $data['status'];
            
            $stmt = $connect->prepare("UPDATE request_documents SET remarks = ?, releaseDate = ?, location = ?, status = ?");
            $stmt->bind_param("ssss", $remarks, $releaseDate, $location, $status);
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
            $stmt = $connect->prepare("UPDATE request_documents SET location = ?");
            $stmt->bind_param("s", $location);
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
                JOIN documents d ON rd.documentId = d.id
                WHERE rd.trackingNumber = ? ");
            $stmt->bind_param("s", $trackingNumber);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows < 0) {
                return array("title" => "Success", "message" => "No record found!", "data" => []);
            }

            $row = $result->fetch_assoc();

            
            $data = array(
                "trackingNumber" => $row['trackingNumber'],
                "remarks" => $row['remarks'],
                "releaseDate" => $row['releaseDate'],
                "location" => $row['location'],
                "status" => $row['status'],
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
            $location = routingRules($documentType);
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
            $stmtRequest->bind_param("iisssssss", $userId, $documentId, $trackingNumber, $location, $status);
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

            if ($accessLevel === 1) {
                return array("title" => "Failed", "message" => "Not Authorized!", "data" => []);
            }

            if ($accessLevel === 3) {
                $stmt = $connect->prepare("SELECT rd.*, d.*
                    FROM request_documents rd
                    JOIN documents d ON rd.documentId = d.id");

            } else if ($accessLevel === 2) {
                $userId = $_SESSION['userId'];
                $department = $_SESSION['department'];

                $stmt = $connect->prepare("SELECT rd.*, d.*
                    FROM request_documents rd
                    JOIN documents d ON rd.documentId = d.id
                    WHERE rd.userId = ? AND department = ?
                ");
                $stmt->bind_param("is", $userId, $department);                
            }

            $stmt->execute();
            $result = $stmt->get_result();
                        
            $data = array();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                                    
                    $data[$row['id']] = array(
                        "trackingNumber" => $row['trackingNumber'],
                        "remarks" => $row['remarks'],
                        "releaseDate" => $row['releaseDate'],
                        "location" => $row['location'],
                        "status" => $row['status'],
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

    function submitDocument($connect, $data) {

    }

    


?>