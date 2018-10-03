<?php
require('./includes/commonutil.php');

$sendto = array();
$sendto["type"] = 'email';
$sendto["data"] = 'davidhui@davesoftllc.com';
$subject = 'test';
$template = array();
$template["location"] = 'acceptedTemplate.hbs';
$template["data"] = array();
$template["data"][0] = array();
$template["data"][0]["name"] = 'name';

sendEmail($sendto,$subject,$template);