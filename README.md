# presearch-node
Automation of API connection with Linux VPS servers

Self-management files and connection of Presearch nodes with their API and VPS servers on Linux



* Settle in the root
``` linux
cd $HOME
``` 


* Create a folder called presearch
``` 
mkdir presearch
``` 


* Located inside the folder
``` linux
cd presearch
``` 


Create the functions file with execution permission
``` 
touch config.php && chmod +x config.php
``` 


Create the functions file with execution permission
``` 
touch methods.php && chmod +x methods.php
``` 


Create the node status check file with execution permission
``` 
touch status.php && chmod +x status.php
``` 


Update the packages
``` 
apt-get update
``` 


Install PHP
``` 
apt-get install php
``` 


Check installed php
``` 
php -v
``` 


Install cron
``` 
sudo apt install cron
``` 


Install dos2unix
``` 
sudo apt-get install dos2unix
``` 


Install curl
``` 
sudo apt-get install php-curl
``` 


Update the packages
``` 
apt-get update
``` 


Convert files to Unix format with dos2unix
``` 
dos2unix config.php
``` 
``` 
dos2unix methods.php
```
``` 
dos2unix status.php
```



Configure the config.php file
``` 
nano config.php
```

Edit
``` php
<?php

    define( 'API_KEY', 'Z3711nQkE9RFgFvLORVgUeIzuUC9zw89q' ); // Private API Key "https://nodes.presearch.org/dashboard" node "stats"
    define( 'REGISTRATION_CODE', '56xD2b09de5fDrPb231e32uMu4426YTkl' ); // Your node registration code "https://nodes.presearch.org/dashboard"
    define( 'THIS_NODE_ADDR', '155.267.145.103' ); // IP address of the current node in this VPS/HOST
    define( 'SEND_ERRORS_TO', 'yourmail@gmail.com' ); //set email notification email address (mark as not spam in gmail)
    define( 'STATUS_OK', '1' );
    define( 'STATUS_ERROR', '0' );
    define( 'RESTART_COMMAND_DOCKER', 'docker stop presearch-node ; docker rm presearch-node ; docker stop presearch-auto-updater ; docker rm presearch-auto-updater ; 
docker run -d --name presearch-auto-updater --restart=unless-stopped -v /var/run/docker.sock:/var/run/docker.sock containrrr/watchtower --cleanup --interval 300 presearch-node ; 
docker pull presearch/node ; docker run -dt --name presearch-node --restart=unless-stopped -v presearch-node-storage:/app/node -e REGISTRATION_CODE='. REGISTRATION_CODE .' presearch/node ; 
docker logs presearch-node' );

?>
``` 


Configure the methods.php file
``` 
nano methods.php
``` 

Edit
``` php
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
``` 


Configure the status.php file
``` 
nano status.php
``` 

Edit
``` php
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
``` 


Verify that the cron service is running
``` 
systemctl status cron
``` 


If it is not running we can activate it and start it and add it as a service at startup
``` 
sudo systemctl enable cron.service
``` 
``` 
sudo systemctl start cron.service
``` 


Open the cron table to configure our scheduled tasks
``` 
crontab -e
``` 


Configure the periodicity of the task every 5 minutes (modify to your preference) add in the last empty line
``` 
*/5 * * * * sudo php -f /root/presearch/status.php
``` 


## Ready...

We can stop docker and wait 5 minutes to verify that it is working well
``` 
docker stop presearch-node ; docker rm presearch-node ; docker stop presearch-auto-updater ; docker rm presearch-auto-updater
``` 
