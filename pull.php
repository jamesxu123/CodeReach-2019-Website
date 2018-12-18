<?php
//echo "Hi";
$postBody = file_get_contents( 'php://input' );

$endpoint['cr']['secret'] = "";
$endpoint['cr']['pushonly'] = true;
$postJSON = json_decode($postBody,true);
//var_dump($postJSON);

$output = null;

if( 'sha1=' . hash_hmac( 'sha1', $postBody, $endpoint['cr']['secret'], false ) === $_SERVER[ 'HTTP_X_HUB_SIGNATURE' ]){
    if($postJSON['repository']['full_name'] == 'jamesxu123/CodeReach-2019-Website'){
        if($endpoint['cr']['pushonly'] || $postJSON['hook']['events'][0] == 'push'){
            //update the repo
            exec('git stash && git pull https://github.com/jamesxu123/CodeReach-2019-Website master',$output);
            var_dump($output);
            echo "Repo has been updated!";
        }
    }
}
var_dump($output);
?>
