<?php

echo "<h2>SMTP Connection Test</h2>";

$host = "mail.technorizen.com";    // SMTP host
$port = 465;                       // Try 587 later
$timeout = 10;

echo "Testing SMTP connection to: <strong>$host:$port</strong><br><br>";

$fp = fsockopen($host, $port, $errno, $errstr, $timeout);

if (!$fp) {
    echo "<span style='color:red;'>❌ Connection Failed</span><br>";
    echo "Error Number: $errno <br>";
    echo "Error Message: $errstr <br>";
} else {
    echo "<span style='color:green;'>✅ Successfully connected to SMTP server!</span><br><br>";

    // Read initial server response
    $response = fgets($fp, 515);
    echo "<pre>Server: $response</pre>";

    fclose($fp);
}

echo "<hr>";

echo "<h3>Testing SSL/TLS Stream</h3>";

$context = stream_context_create([
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
]);

$connection = stream_socket_client("ssl://$host:$port", $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $context);

if (!$connection) {
    echo "<span style='color:red;'>❌ SSL Connection Failed</span><br>";
    echo "Error Number: $errno <br>";
    echo "Error Message: $errstr <br>";
} else {
    echo "<span style='color:green;'>✅ SSL/TLS Connection OK</span><br>";
}

echo "<hr>";

echo "<h3>Try TLS (STARTTLS) on port 587</h3>";

$connection2 = fsockopen($host, 587, $errno, $errstr, 10);

if (!$connection2) {
    echo "<span style='color:red;'>❌ TLS (587) Connection Failed</span><br>";
    echo "Error Number: $errno <br>";
    echo "Error Message: $errstr <br>";
} else {
    echo "<span style='color:green;'>✅ TLS Port 587 is OPEN</span><br>";
}

echo "<br><br><strong>Test completed.</strong>";

?>
