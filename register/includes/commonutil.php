<?php
function sqlQuery($sql){
    require('./config.php');
    // Create connection
    $conn = new mysqli($_CONFIG['db_host'], $_CONFIG['db_user'], $_CONFIG['db_password'], $_CONFIG['db_name']);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    
    $result = $conn->query($sql);
    $conn->close();
    return $result;
}

function verifyToken($token){
    require('./config.php');
    try{
        $tokenInfo = JWT::decode($token, $_CONFIG['jwt_secret']);
    }
    catch(Exception $e){
        $tokenInfo = new stdClass();
        $tokenInfo->id = -1;
        return $tokenInfo;
    }
    
    if($tokenInfo->issued < time()-3600){
        $id = $tokenInfo->id;
        $timeResult = sqlQuery("SELECT lastChanged from users where id='$id'");
        if($tokenInfo->issued < intval($timeResult->fetch_assoc()['lastChanged'])){
            return $tokenInfo;
        }
        else{
            //print('Ok');
            return $tokenInfo;
        }
    }
    else{
        //print('Ok');
        return $tokenInfo;
    }
}

function sendEmail($sendto,$subject,$template){
    require('./config.php');
    header('Content-Type: text/plain');
    $returnArray = array();
    $returnArray["success"] = false;
    
    if($sendto["type"] == 'email'){
        //lookup the name
        $email = $sendto["data"];
        $nameResult = sqlQuery("SELECT name from users where email='$email'");
        if($nameResult->num_rows > 0){
            $name = $nameResult->fetch_assoc()["name"];
        }
        else{
            $returnArray["message"] = "Unable to find a user with the specified email";
        }
    }
    else if($sendto["type"] == 'id' || $sendto["type"] == 'username'){
        //lookup the name and email
        if($sendto["type"] == 'id'){
            $id = $sendto["data"];
            $username = '';
        }else{
            $username = $sendto["data"];
            $id = '';
        }
        
        $dataResult = sqlQuery("SELECT name,email from users where id='$id' or username='$username'");
        if($dataResult->num_rows > 0){
            $name = $dataResult->fetch_assoc()["name"];
            $email = $dataResult->fetch_assoc()["username"];
        }
        else{
            $returnArray["message"] = "Unable to find a user with the specified username or id";
        }
        
    }
    
    //populate the template
    $templateLocation = $template["location"];
    $templateHTML = file_get_contents('./templates/'.$templateLocation);
    $templateData = $template["data"];
    
    foreach($templateData as $dataSet){
        if($dataSet["name"] == "name"){
            $dataSet["value"] = $name;
        }
        $tag = "<".$dataSet["name"].">";
        $value = $dataSet["value"];
        $templateHTML = str_replace($tag,$value,$templateHTML);
    }
    
    //setup the request
    $requestContent = array();
    $requestContent["personalizations"] = array();
    $requestContent["personalizations"][0] = array();
    $requestContent["personalizations"][0]["to"] = array();
    $requestContent["personalizations"][0]["to"][0] = array();
    $requestContent["personalizations"][0]["to"][0]["email"] = $email;
    $requestContent["personalizations"][0]["to"][0]["name"] = $name;
    $requestContent["personalizations"][0]["subject"] = $subject;
    $requestContent["from"] = array();
    $requestContent["from"]["email"] = 'noreply@codereach.ca';
    $requestContent["from"]["name"] = 'CodeReach';
    $requestContent["reply_to"] = array();
    $requestContent["reply_to"]["email"] = 'hello@codereach.ca';
    $requestContent["reply_to"]["name"] = 'CodeReach Support';
    $requestContent["content"] = array();
    $requestContent["content"][0] = array();
    $requestContent["content"][0]["type"] = 'text/plain';
    $requestContent["content"][0]["value"] = 'Your email client does not support the viewing of HTML emails';
    $requestContent["content"][1] = array();
    $requestContent["content"][1]["type"] = 'text/html';
    $requestContent["content"][1]["value"] = $templateHTML;
    
    //send the email
    
    $requestContentString = json_encode($requestContent);
    
    $returnArray["debug"] = $requestContentString;
    
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://api.sendgrid.com/v3/mail/send");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestContentString);
    curl_setopt($ch, CURLOPT_POST, 1);
    
    $headers = array();
    $headers[] = "Authorization: Bearer ".$_CONFIG['sendgrid_key'];
    $headers[] = "Content-Type: application/json";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
    
    $result = curl_exec($ch);
    $returnArray["debug2"] = $result;
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close ($ch);
    return $returnArray;
}

?>