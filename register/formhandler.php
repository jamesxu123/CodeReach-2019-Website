<?php
require('./config.php');
require('./includes/commonutil.php');

$status = array();
$status['code'] = 200;
$status['message'] = 'OK';

function generateRandomString($length = 16) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if(
    !isset($_POST['fullname'])||
    !isset($_POST['username'])||
    !isset($_POST['password'])||
    !isset($_POST['email'])||
    !isset($_POST['birthday'])||
    !isset($_POST['grade'])||
    !isset($_POST['experience'])||
    !isset($_POST['currentschool'])||
    !isset($_POST['gender'])||
    !isset($_POST['shirtsize'])||
    !isset($_POST['parent_fullname'])||
    !isset($_POST['parent_email'])||
    !isset($_POST['parent_phone'])||
    !isset($_POST['emergencyone'])||
    !isset($_POST['emergencytwo'])||
    !isset($_POST['canwalk'])
    ){
        echo 'Invalid submission!';
        var_dump($_POST);
        die();
    }

$fullname = $_POST['fullname'];
$username = str_replace(' ', '', strtolower($_POST['username']));
$password = password_hash($_POST['password'],PASSWORD_DEFAULT);
$email = str_replace(' ', '', strtolower($_POST['email']));
$birthday = $_POST['birthday'];
$grade = $_POST['grade'];
$experience = $_POST['experience'];
$currSchool = $_POST['currentschool'];
$gender = $_POST['gender'];
$shirtSize = $_POST['shirtsize'];
$parentFullName = $_POST['parent_fullname'];
$parentEmail = $_POST['parent_email'];
$parentPhone = $_POST['parent_phone'];
$emergencyOne = $_POST['emergencyone'];
$emergencyTwo = $_POST['emergencytwo'];
$canWalk = $_POST['canwalk'];

$accountLookup = sqlQuery("SELECT username,email from users where username='$username' or email='$email'");


if($accountLookup->num_rows > 0){
    $status['code'] = 400;
    
    $accountAssoc = $accountLookup->fetch_assoc();
    if($accountAssoc["username"] == $username){
        $status['message'] = "The username is already taken!";
    }
    else if($accountAssoc["email"] == $email){
         $status['message'] = "The email provided is already being used!" .$accountAssoc["email"].$email ;
    }
    
}
else{
    $sql = "INSERT INTO applications (fullname, email, birthday, grade, experience, currentschool, gender, shirtsize, parent_fullname, parent_email, parent_phone, emergencyone, emergencytwo, canwalk) VALUES ('$fullname', '$email', '$birthday', '$grade', '$experience', '$currSchool', '$gender', '$shirtSize', '$parentFullName', '$parentEmail', '$parentPhone', '$emergencyOne', '$emergencyTwo', '$canWalk')";
    $result = sqlQuery($sql);
    if(!$result){
        $status['code'] = 500;
        $status['message'] = 'Unable to update the database';
    }
    
    $appID = sqlQuery("SELECT id from applications where email='$email'")->fetch_assoc()['id'];
    $verifyToken = generateRandomString();
    $sql = "INSERT into users (username, name, email, password, role, application, verifytoken) values ('$username','$fullname','$email','$password',2,'$appID','$verifyToken')";
    $accountResult = sqlQuery($sql);

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
    
     
    
    if(!$accountResult){
        $status['code'] = 500;
        $status['message'] = 'Unable to update the database - create user';
        $status['sql'] = $sql;
    }
}



$resultStr = json_encode($status);
print($resultStr);
?>