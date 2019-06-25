<?php
// using SendGrid's PHP Library
// https://github.com/sendgrid/sendgrid-php
//require 'vendor/autoload.php'; // If you're using Composer (recommended)
// Comment out the above line if not using Composer
require("./sendgrid-php.php");
// If not using Composer, uncomment the above line

//$dotenv = new Dotenv\Dotenv(__DIR__);
//$dotenv->load();

$data = array();
$firstname = $_POST['name'];
$email_addr = $_POST['email'];
$usermessage = $_POST['message'];

$slackmsg = array(
    'text' => "Name: " . $firstname . "\nEmail: " . $email_addr . "\nMessage: " . $usermessage
);

$URL = 'https://hooks.slack.com/services/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';

$ch = curl_init($URL);

curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($slackmsg, '', '&'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$email = new \SendGrid\Mail\Mail();
$email->setFrom("form@codereach.ca", "Form Response");
$email->setReplyTo($email_addr);
$email->setSubject("Message From " . $firstname);
$email->addTo("hello@codereach.ca", "CodeReach");
$email->addTo("admin@codereach.ca", "CodeReach Admin");
$email->addContent(
    "text/plain", "Name: " . $firstname . "\nEmail: " . $email_addr . "\nMessage: " . $usermessage
);
$msg_file = fopen("email.txt","r");
$msg = fread($msg_file, filesize("email.txt"));
fclose($msg_file);
$email2 = new \SendGrid\Mail\Mail();
$email2->setFrom("noreply@codereach.ca", "CodeReach Team");
$email2->setSubject("Your contact request has been received");
$email2->addTo($email_addr);
$email2->addContent(
    "text/plain", "Hey " . $firstname . ",\n\nWe've received your contact request and will be responding in the next few days\n\nCodeReach Team");
$sendgrid = new \SendGrid($_ENV["SENDGRID_API_KEY"]);

$response = $sendgrid->send($email);
$response2 = $sendgrid->send($email2);
//print $response->statusCode() . "\n";
//print_r($response->headers());
//print $response->body() . "\n";

$data['success'] = true;
echo json_encode($data);
?>
