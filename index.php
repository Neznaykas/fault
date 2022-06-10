<?php

ini_set("memory_limit", "512M");
ini_set('max_execution_time', 0);

use Failure\LogParser;
use Failure\Generator;

require_once __DIR__ . '/vendor/autoload.php';

/* Console mod */
if (isset($argv)) {
    $commands = [];

    for ($i = 1; $i < $argc; $i++) {
        $commands[$i - 1] = $argv[$i];
    }

    (new LogParser("php://stdin", floatval($commands[1]), floatval($commands[3])))->sort()->console();
} else {
    /* Interactive: localhost */
    $time_start = microtime(true);

    $logfile = __DIR__ . '/access.log';
    $nginx_log = '/var/log/nginx/localhost.access_log';

    if (file_exists($nginx_log)) {
        /* очень медленно но лог настоящий */
        if (filesize($nginx_log) < 10000) {
            if (rand(1, 25) == 1) {
                header("HTTP/1.1 500 Internal Server Error");
                header("Refresh:0");
            } else {
                header("Refresh:0");
            }
        }

        echo '<b>Nginx analyze: </b><br>';
        $logs = new LogParser($nginx_log, 100, 1);

        echo 'Count: ' . $logs->count . ' Errors: ' . $logs->errors . '<br>';
        
        (new LogParser($nginx_log, 100, 1))->sort()->print();
    }

    echo '<b>Random generate analyze: </b><br>';
    //new Generator($logfile, 30, 100);

    $logs = new LogParser($logfile, 99, 60);
    $logs->intervals->sort();
    $logs->print();

    echo 'Count: ' . $logs->count . ' Errors: ' . $logs->errors . '<br>';

    /*echo '<b>Items: </b><br>';
    print_r((new LogParser($logfile, 95, 60))->intervals->sort()->get());

    echo '<br><br>';*/

    $time_end = microtime(true);
    $execution_time = ($time_end - $time_start);
    echo '<b>Total Execution Time:</b> ' . $execution_time . '<br><br>';
}
