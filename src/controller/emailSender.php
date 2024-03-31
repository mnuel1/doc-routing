<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php'; // Load Composer's autoloader

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    function sendEmail($email, $body) {
        try {    
            $mail = new PHPMailer();
    
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.hostinger.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'doc_routing@resiboph.site';                     //SMTP username
            $mail->Password   = 'docrouting1A)';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                         //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
            //Server settings
            $mail->CharSet = 'UTF-8';
            $mail->IsMAIL();
            $mail->IsSMTP();
            $mail->Subject = "Request Document Details";
            $mail->From = "DocRoute@docroute.site";
            $mail->FromName = "no reply";
            $mail->IsHTML(true);
            $mail->AddAddress($email); // To mail id
            
            
            $mail->MsgHTML($body);
            $mail->WordWrap = 50;
            $mail->Send();
            $mail->SmtpClose();
            
            return array("title" => "Success", "message" => "Email Succesfully Sent", "data" => []);
        } catch (Exception $e) {
            return array("title" => "Success", "message" => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}", "data" => []);
        }  
    }
        
    
?>
