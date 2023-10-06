<?php
$ch = curl_init();
$addr='vladandrei.oleksik@gmail.com';
$url = 'https://script.google.com/macros/s/AKfycby3CTJg7tvy2oUoDOSiYdt9gfq2dzFKH5f7RpRjEgmi25BR4muQgWm3Vw9MoR8v6Gxa1g/exec?addr=' . $addr . '&ip=192.168.0.1&time=12:30';
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); 

$result = curl_exec($ch);
curl_close($ch);

echo "OK";

?>