<?php
/*
File: api.php
COPYRIGHT (c) 2018 David Hui. All rights reserved. Licensed for use, reuse, and modification by CodeReach for any purpose.
*/
require('./config.php');
require('./includes/commonutil.php');
require('./includes/jwt_helper.php');
$data = json_decode(file_get_contents('php://input'), true);
header('Content-Type: application/json');

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

$token = $data['token'];
$userArray = verifyToken($token);
$action = strtolower($data['action']);

if($userArray->id != -1){
    //authenticated
    //upgrade the request status
    $status['code'] = 400;
    $status['message'] = 'Bad Request';
    
    //get the user's app id
    $userAppId = sqlQuery("SELECT application from users where id='$userArray->id'")->fetch_assoc()['application'];
    
    switch($action){
        case 'getapplication':
            if($userArray->role < 2 || intval($data['appid']) == intval($userAppId)){
                $appid = $data['appid'];
                $application = sqlQuery("SELECT * from applications where id='$appid'");
                if($application->num_rows > 0){
                    $status['code'] = 200;
                    $status['message'] = 'OK';
                    $status['application'] = $application->fetch_assoc();
                }
                else{
                    $status['message'] = 'The application with the specified id does not exist';
                }
            }else{
                $status['code'] = 403;
                $status['message'] = 'Forbidden';
            }
            break;
        case 'allapplications':
            if($userArray->role < 2){
                $applications = sqlQuery("SELECT * from applications");
                if($applications->num_rows > 0){
                    $status['code'] = 200;
                    $status['message'] = 'OK';
                    $status['applications'] = array();
                    $i = 0;
                    while($row = $applications->fetch_assoc()){
                        $status['applications'][$i] = $row;
                        $i++;
                    }
                }
                else{
                    $status['message'] = 'There are no applications';
                }
            }else{
                $status['code'] = 403;
                $status['message'] = 'Forbidden';
            }
            break;
        case 'denyapplication':
            if($userArray->role < 1){
                $appid = $data['appid'];
                $application = sqlQuery("UPDATE applications set status='1' where id='$appid'");
                if($application){
                    $status['code'] = 200;
                    $status['message'] = 'OK';
                }
                else{
                    $status['code'] = 500;
                    $status['message'] = 'Unable to update the database - deny application';
                }
            }else{
                $status['code'] = 403;
                $status['message'] = 'Forbidden';
            }
            break;
        case 'acceptapplication':
            if($userArray->role < 1){
                $appid = $data['appid'];
                $confirmToken = generateRandomString();
                $application = sqlQuery("UPDATE applications set status='2',confirmtoken='$confirmToken' where id='$appid'");
                $userEmail = sqlQuery("SELECT email from applications where id='$appid'")->fetch_assoc()["email"];
                if($application){
                    //mail da email
                    $sendto = array();
                    $sendto["type"] = 'email';
                    $sendto["data"] = $userEmail;
                    $subject = 'Congratulations! You have been accepted into CodeReach!';
                    $template = array();
                    $template["location"] = 'acceptedTemplate.hbs';
                    $template["data"] = array();
                    $template["data"][0] = array();
                    $template["data"][0]["name"] = 'name';
                    $template["data"][1] = array();
                    $template["data"][1]["name"] = 'confirmby';
                    $template["data"][1]["value"] = 'October 10, 2018';
                    $template["data"][2] = array();
                    $template["data"][2]["name"] = 'tourl';
                    $template["data"][2]["value"] = 'https://register.codereach.ca/dash/pages/login.php?confirmtoken='.$confirmToken.'&appid='.$appid;
                    $status['message'] = sendEmail($sendto,$subject,$template);
                    
                    $status['code'] = 200;
                    //$status['message'] = 'OK';
                }
                else{
                    $status['code'] = 500;
                    $status['message'] = 'Unable to update the database - accept application';
                }
            }else{
                $status['code'] = 403;
                $status['message'] = 'Forbidden';
            }
            break;
        case 'waitlistapplication':
            if($userArray->role < 1){
                $appid = $data['appid'];
                $application = sqlQuery("UPDATE applications set status='3' where id='$appid'");
                if($application){
                    $status['code'] = 200;
                    $status['message'] = 'OK';
                }
                else{
                    $status['code'] = 500;
                    $status['message'] = 'Unable to update the database - waitlist application';
                }
            }
            break;
        case 'waitlistall':
            if($userArray->role < 1){
                $waitlist = sqlQuery("UPDATE applications set status='2' where status='0'");
                if($application){
                    $status['code'] = 200;
                    $status['message'] = 'OK';
                }
                else{
                    $status['code'] = 500;
                    $status['message'] = 'Unable to update the database - waitlist all applications';
                }
            }else{
                $status['code'] = 403;
                $status['message'] = 'Forbidden';
            }
            break;
        case 'getstats':
            if($userArray->role < 2){
                $stats = sqlQuery("SELECT status from applications");
                $statArray =array();
                $statArray['total'] = 0;
                $statArray['nreviewed'] = 0;
                $statArray['accepted'] = 0;
                $statArray['waitlisted'] = 0;
                $statArray['rejected'] = 0;
                $statArray['manual'] = 0;
                if($stats->num_rows > 0){
                    while($row = $stats->fetch_assoc()){
                        $statArray['total']+=1;
                        switch($row['status']){
                            case "0":
                                $statArray['unreviewed']+=1;
                                break;
                            case "1":
                                $statArray['rejected']+=1;
                                break;
                            case "2":
                                $statArray['waitlisted']+=1;
                                break;
                            case "3":
                                $statArray['accepted']+=1;
                            default:
                                $statArray['manual']+=1;
                        }
                    }
                }
                $status['code'] = 200;
                $status['message'] = 'OK';
                $status['stats'] = $statArray;
            }else{
                $status['code'] = 403;
                $status['message'] = 'Forbidden';
            }
            break;
    }
}

$resultStr = json_encode($status);
print($resultStr);
?>