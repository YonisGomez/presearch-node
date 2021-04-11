<?php

require_once( 'config.php');

function get_json_data(){
    $url = 'https://nodes.presearch.org/api/nodes/status/'.API_KEY.'?stats=true';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $headers = array(
       "Accept: application/json",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $resp = curl_exec($curl);
    curl_close($curl);
    return json_decode($resp,true);
}

function send_mail_notification($node_description, $time_in_current_state){
    $subject = "Node Status Offline";
    $txt = "\nRebooting node: $node_description";
    $txt .= "\nFrom IP: " . THIS_NODE_ADDR;
    $txt .= "\nTime in current state: $time_in_current_state";
    $headers = "From: " . SEND_ERRORS_TO . "\r\n" . "CC: " . SEND_ERRORS_TO;
    mail(SEND_ERRORS_TO,$subject,$txt,$headers);
}

function restart_node(){
    echo shell_exec(RESTART_COMMAND_DOCKER);
}

?>
