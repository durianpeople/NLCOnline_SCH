<?php

use NLC\Base\Announcement;

header("Cache-Control: no-cache");
header("Content-Type: text/event-stream\n\n");

$counter = rand(1, 10);
while (true) { 
    if(Announcement::hasUnread()){
        echo "event: notified\n";
        $curDate = date(DATE_ISO8601);
        //   echo 'data: {"time": "' . $curDate . '"}';
        echo 'data: {"has_unread": true}';
        echo "\n\n";

        ob_end_flush();
        flush();
    }
    sleep(15);
}
