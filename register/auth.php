<?php
/*
File: auth.php
COPYRIGHT (c) 2018 David Hui. All rights reserved. Licensed for use, reuse, and modification by CodeReach for any purpose.
*/
require('./config.php');
require('./includes/commonutil.php');
require('./includes/jwt_helper.php');
$data = json_decode(file_get_contents('php://input'), true);
header('Content-Type: application/json');
/*
POST fields:
username: username
password: password
*/

function generateRandomString($length = 16) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$status = array();
$status['code'] = 403;
$status['message'] = 'Forbidden';

if(isset($data['signup'])){
    $username = $data['username'];
    $password = password_hash($data['password'],PASSWORD_DEFAULT);
    $fullname = $data['fullname'];
    $email = $data['email'];
    
    $email = isset($data['email']) ? trim($data['email']) : null;

    // List of allowed domains
    $allowed = [
        'codereach.ca'
    ];
    
    // Make sure the address is valid
    if (filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        // Separate string by @ characters (there should be only one)
        $parts = explode('@', $email);
    
        // Remove and return the last part, which should be the domain
        $domain = array_pop($parts);
    
        // Check if the domain is in our list
        
        $accountLookup = sqlQuery("SELECT username,email from users where username='$username' or email='$email'");
        
        if($accountLookup->num_rows > 0){
            $status['code'] = 400;
            
            $accountAssoc = $accountLookup->fetch_assoc();
            if($accountAssoc["username"] == $username){
                $status['message'] = "The username is already taken!";
            }
            else if($accountAssoc["email"] == $email){
                 $status['message'] = "The email is already being used!";
            }
            
        }else{
            $verifytoken = generateRandomString();
            if ( ! in_array($domain, $allowed))
            {
                $status['code'] = 400;
                $status['message'] = 'This signup form is for staff only. Please <a href="../../form">click here</a> to register as a student.';
            }
            else{
                //allowed
                $result = sqlQuery("INSERT into users (username, name, email, password, role) values ('$username','$fullname','$email','$password','1','$verifytoken')");
                if($result){
                    
                    //mail da email
                    $sendto = array();
                    $sendto["type"] = 'email';
                    $sendto["data"] = $email;
                    $subject = 'Please verify your email';
                    $template = array();
                    $template["location"] = 'emailVerifyTemplate.hbs';
                    $template["data"] = array();
                    $template["data"][0] = array();
                    $template["data"][0]["name"] = 'tourl';
                    $template["data"][0]["value"] = 'https://register.codereach.ca/dash/pages/login.php?verifytoken='.$verifyToken.'&username='.$username;
                    sendEmail($sendto,$subject,$template);
                    
                    $status['code'] = 200;
                    $status['message'] = 'OK';
                }else{
                    $status['code'] = 500;
                    $status['message'] = 'Unable to update the database - create user';
                } 
            }
        }
        
    }
    
}
else if(isset($data['verifytoken']) and isset($data['username'])){
    $verifyToken = $data['verifytoken'];
    $username = $data['username'];
    
    $tokenExists = sqlQuery("SELECT id from users where username='$username' and verifytoken='$verifyToken'");
    if($tokenExists->num_rows > 0){
        $updateResult = sqlQuery("UPDATE users set confirmed='1',verifytoken='0' where username='$username'");
        if($updateResult){
            $status['code'] = 200;
            $status['message'] = 'OK';
        }
        else{
            $status['code'] = 500;
            $status['message'] = 'Unable to update the database - set confirm';
        }
    }
    else{
        $status['code'] = 400;
        $status['message'] = 'The specified token is invalid!';
    }
}
else if(isset($data['confirmtoken']) and isset($data['appid'])){
    $confirmToken = $data['confirmtoken'];
    $appid = $data['appid'];
    
    $tokenExists = sqlQuery("SELECT id,email from applications where id='$appid' and confirmtoken='$confirmToken'");
    if($tokenExists->num_rows > 0){
        $updateResult = sqlQuery("UPDATE applications set status='4',confirmtoken='0' where id='$appid'");
        if($updateResult){
            
            //mail da email
            $sendto = array();
            $sendto["type"] = 'email';
            $sendto["data"] = $tokenExists->fetch_assoc()["email"];
            $subject = 'Thank you for confirming your spot!';
            $template = array();
            $template["location"] = 'confirmedTemplate.hbs';
            $template["data"] = array();
            $template["data"][0] = array();
            $template["data"][0]["name"] = 'tourl';
            $template["data"][0]["value"] = 'https://register.codereach.ca/dash/pages/index.php';
            sendEmail($sendto,$subject,$template);
            
            $status['code'] = 200;
            $status['message'] = 'OK';
        }
        else{
            $status['code'] = 500;
            $status['message'] = 'Unable to update the database - set confirm';
        }
    }
    else{
        $status['code'] = 400;
        $status['message'] = 'The specified token is invalid!';
    }
}
else{
    $username = $data['username'];
    $password = $data['password'];
    
    $result = sqlQuery("SELECT id,name,email,password,role,application from users where confirmed='1' and (username='$username' or email='$username')");
    if($result->num_rows > 0){
        $result_assoc = $result->fetch_assoc();
        //print(password_hash('CreateTV123',PASSWORD_DEFAULT));
        if(password_verify($password,$result_assoc['password'])){
            //authenticated, issue a token
            $tokenArray = array();
            $tokenArray['id'] = intval($result_assoc['id']);
            $tokenArray['expiry'] = time() + 86400;
            $tokenArray['issued'] = time();
            $tokenArray['username'] = $username;
            $tokenArray['name'] = $result_assoc['name'];
            $tokenArray['email'] = $result_assoc['email'];
            $tokenArray['role'] = intval($result_assoc['role']);
            $tokenArray['application'] = intval($result_assoc['application']);
            // Admin - Role 0
            // User - Role 1
            $token = JWT::encode($tokenArray,$_CONFIG['jwt_secret']);
            $status['code'] = '200';
            $status['message'] = 'OK';
            $status['token'] = $token;
        }
        else{
            $status['code'] = 401;
            $status['message'] = 'Unauthorized';
        }
    }
    else{
        $status['code'] = 401;
        $status['message'] = 'Unauthorized';
    }

}

$resultStr = json_encode($status);
print($resultStr);

?>