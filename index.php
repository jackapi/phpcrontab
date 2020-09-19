<?php
require_once 'vendor/autoload.php';

$crontab = [
    ['1 * * * *', 'test1', ''],
    ['1 * * * *', 'test2', ''],
];

$server = new WebSocket\Server([
    'timeout' => 60, // 1 minute time out
    'port' => 9000,
]);
while ($server->accept()) {
    try {
        $server->send('1');
        $message = $server->receive();
        var_dump($message);

        // Act on received message
        // Break while loop to stop listening
    } catch (\WebSocket\ConnectionException $e) {
        // Possibly log errors
    }
}
$server->close();