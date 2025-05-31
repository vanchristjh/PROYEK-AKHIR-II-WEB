<?php
$ch = curl_init('http://127.0.0.1:8000/check-pivot-tables');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>
