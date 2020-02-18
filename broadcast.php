<?php
if (!isset($_SESSION)) {
    session_start();
}

if(!isset($_SESSION['user'])){
    session_destroy();
    Header('Location: login.php');
}
include 'booking.php';

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

echo "data: Message is: {$_SESSION['broadcast']}\n\n";
flush();

?>