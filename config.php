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
