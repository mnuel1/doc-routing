<?php
   
    function generateToken ($payload) {
        $env = parse_ini_file('.env');

        $secretKey = $env["SECRET_KEY"];
       
        // Encode the payload as JSON
        $payloadJson = json_encode($payload);
    
        // Base64url encode the payload
        $payloadBase64 = base64_encode($payloadJson);
        $payloadBase64Url = str_replace(['+', '/', '='], ['-', '_', ''], $payloadBase64);
    
        // Create the signature using HMAC-SHA256
        $signature = hash_hmac('sha256', $payloadBase64Url, $secretKey, true);
        $signatureBase64Url = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
        // Combine the payload and signature to create the token
        $token = $payloadBase64Url . '.' . $signatureBase64Url;
    
        return $token;
        
    }

    function logFailedLoginAttempt($connect, $ipAddress){
                
        try {
            $timestamp = date('Y-m-d H:i:s');
            $stmt = $connect->prepare("INSERT INTO login_attempts (ipAddress, timestamp) 
                VALUES (?, ?)");
            $stmt->bind_param("ss", $ipAddress, $timestamp);
            $stmt->execute();
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }
    function incrementLoginAttempt() {
            
        $_SESSION['login_attempts'] = isset($_SESSION['login_attempts']) ? $_SESSION['login_attempts'] + 1 : 1;
        $_SESSION['last_attempt_time'] = time();
        
    }

    function isUserLockedOut() {
        $attempts = isset($_SESSION['login_attempts']) ? $_SESSION['login_attempts'] : 0;
        $last_attempt_time = isset($_SESSION['last_attempt_time']) ? $_SESSION['last_attempt_time'] : null;

        if ($attempts >= 7 && $last_attempt_time) {
            $current_time = time();
            $wait_time = 3600; // 1 hour in seconds
            return ($current_time - $last_attempt_time) < $wait_time;
        }

        return false;
    }


    function login ($connect, $username, $password) {

        try {
            if (isUserLockedOut()) {

                $stmt = $connect->prepare("SELECT * FROM user_cred WHERE $username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();

                $result = $stmt->get_result();

                if ($result->num_rows < 0) {
                    logFailedLoginAttempt($connect, $_SERVER['REMOTE_ADDR']);
                    incrementLoginAttempt();
                    return array("title" => "Failed", "message" => "Username does not exist. Please verify your username.", "data" => []);
                }

                $row = $result->fetch_assoc();

                if (!password_verify($password, $row['password'])) {
                    logFailedLoginAttempt($connect, $_SERVER['REMOTE_ADDR']);
                    incrementLoginAttempt();
                    return array("title" => "Failed", "message" => "Passwords do not match. Please verify your password.", "data" => []);
                }
                 
                $payload = array('user_id' => $row['userId']);
                
                $token = generateToken($payload);
                
                setcookie("token", $token, time() + (86400 * 30), "/"); // saves token to cookie 

                $getDepartmentStmt = $connect->prepare("SELECT department, name, email FROM user_info
                    WHERE userId = ?");
                $getDepartmentStmt->bind_param("i", $row['userId']);
                $getDepartmentStmt->execute();
                $result = $getDepartmentStmt->get_result()->fetch_assoc();
                                
                $_SESSION['userId'] = $row["userId"];
                $_SESSION['username'] = $row["username"];                
                $_SESSION['accessLevel'] = $row['accessLevel'];
                $_SESSION['department'] = $result['department'];
                $_SESSION['name'] = $result['name'];
                $_SESSION['email'] = $result['email'];
                                

                return array("title" => "Success", "message" => "Login Successful", "data" => []);
              
                

            } else {
                $last_attempt_time = isset($_SESSION['last_attempt_time']) ? $_SESSION['last_attempt_time'] : null;
                $current_time = time();
                $wait_time = 3600; // 1 hour in seconds

                if ($last_attempt_time) {
                    $time_difference = $current_time - $last_attempt_time;
                    $remaining_time = $wait_time - $time_difference;
                    $remaining_hours = ceil($remaining_time / 3600); // Convert remaining seconds to hours

                    if ($remaining_hours > 0) {
                        $msg = 'You have exceeded the maximum number of login attempts. Please try again after ' . $remaining_hours . ' hour(s).';
                        return array("title" => "Failed", "message" => $msg, "data" => []);
                    } else {
                        // Reset login attempts if waiting time is over
                        $_SESSION['login_attempts'] = 0;
                        $_SESSION['last_attempt_time'] = null;
                    }
                }                
            }            
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }
?>
