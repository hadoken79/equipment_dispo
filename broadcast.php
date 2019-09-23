<?php
include 'booking.php';
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');


echo "data: Message is: {$_SESSION['broadcast']}\n\n";
flush();


?>