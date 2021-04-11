<?php

require_once( 'config.php');
require_once( 'methods.php');

$node_data = get_json_data();

foreach($node_data['nodes'] as $key=>$val){

    $node_description           = $val['meta']['description'];
    $remote_addr                = $val['meta']['remote_addr'];
    $node_status                = $val['status']['connected'] != 0 ? $val['status']['connected'] : 0;
    $minutes_in_current_state   = $val['status']['minutes_in_current_state'];
    $time_in_current_state      = date('H\h:i\m:s\s', mktime(0,$minutes_in_current_state));

    if( (THIS_NODE_ADDR == $remote_addr) && ($node_status == STATUS_ERROR) ) {
        restart_node();
        send_mail_notification($node_description, $time_in_current_state);
    }
}

?>
