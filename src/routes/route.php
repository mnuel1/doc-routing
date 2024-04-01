<?php

    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/src/controller/documentController.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/src/controller/userController.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/src/controller/reportController.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/src/controller/emailSender.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/src/middleware/verify.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/src/middleware/apiRateLimiter.php';

    $isVerified = verify();
    if ($isVerified["data"] === false){
        echo $isVerified["title"] . " ";
        echo $isVerified["message"];
        exit;
    }
    
    // all response is meron gantong format
    // RESPONSE
    // array("title" => "Success/Failed/Internal Error", 
    // "message" => "This is an message", "data" => [either empty or meron]

    // for more specific info pls do check nalang each endpoints
    


    // use to set the status of a requested document to RELEASED
    // most likely eto na yung last process
    // mag tritrigger na isend yung document sa email nung nag request
    // to notify them with doc attached
    // route: document/release
    if ($_SERVER['REQUEST_METHOD'] === 'POST' 
        && $_POST['route'] === 'document/release') {
            $data = [
                'requestedDocId' => $_POST['requestedDocId']
            ];
            $response = releaseDocument($connect, $data);
        
            echo json_encode($response);        
    }

    // use to submit a request document
    // route: document/request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' 
        && $_POST['route'] === 'document/request') {

            $data = [
                'recipient' =>  $_POST['recipient'],
                'department' => $_POST['department'],
                'documentType' => $_POST['documentType'],
                'note' => $_POST['note'],
                'purpose' => $_POST['purpose'],
                'actionsNeeded' => $_POST['actionsNeeded'],
                'documentTitle' => $_POST['documentTitle'],
                'documentFile' => $_POST['documentFile'],
            ];
        
            $response = requestDocument($connect, $data);
        
            echo json_encode($response);
    }

    // use to get all the documents
    // for displaying them in table
    // route: document/get/all
    if ($_SERVER['REQUEST_METHOD'] === 'POST' 
        && $_POST['route'] === 'document/get/all') {
                    
            $response = getAllDocuments($connect);
        
            echo json_encode($response);
    }

    // use to search the tracking number of the requested doc
    // route: document/get
    if ($_SERVER['REQUEST_METHOD'] === 'POST' 
        && $_POST['route'] === 'document/get') {
            $data = [
                'trackingNumber' => $_POST['trackingNumber']
            ];
            $response = searchTrackNumber($connect, $data);
        
            echo json_encode($response);
    }
    // use to update kung nasaan na yung requested doc
    // mainly use para lang ichange yung location
    // example 
    // nasa tup na, nasa registrar na
    // pag irerelease na call the document/release
    // route: document/get
    if ($_SERVER['REQUEST_METHOD'] === 'POST' 
        && $_POST['route'] === 'document/update/location') {

            $_POST['location'] = 'Release';
            $_POST['requestId'] = 2;
            $data = [
                'location' => $_POST['location'],
                'requestId' => $_POST['requestId'],                
            ];
        
            $response = updateLocRequestedDoc($connect, $data);
        
            echo json_encode($response);
    }

    // use to update the requestedDoc 
    // mainly use for the intial accepting/rejecting
    // ng document
    // route: document/update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' 
        && $_POST['route'] === 'document/update') {            
            $data = [
                'remarks' => $_POST['remarks'],
                'releaseDate' => $_POST['releaseDate'],
                'location' => $_POST['location'],
                'status' => $_POST['status'],
                'requestId' => $_POST['requestId'],                
            ];
        
            $response = updateRequestedDoc($connect, $data);
        
            echo json_encode($response);
    }

    // use para ireturn yung info ng user
    // for user info page
    // route: user/get
    if ($_SERVER['REQUEST_METHOD'] === 'POST' 
        && $_POST['route'] === 'user/get') {
                 
            $response = getUserData($connect);
        
            echo json_encode($response);
    }

    // use para don sa 4 boxes
    // route: report/get
    if ($_SERVER['REQUEST_METHOD'] === 'POST' 
        && $_POST['route'] === 'report/get') {
                 
            $response = getQuickSummaryReport($connect);
        
            echo json_encode($response);
    }
    // use para mag send ng email
    // route: email/send
    if ($_SERVER['REQUEST_METHOD'] === 'POST' 
        && $_POST['route'] === 'email/send') {
                
            $email = "abc888043@gmail.com";
            $file = $_POST['file'];

            $response = sendEmail($email, $file);
        
            echo json_encode($response);
    }


?>
