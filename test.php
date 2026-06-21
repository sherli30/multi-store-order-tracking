<?php
$ch = curl_init('http://127.0.0.1:8000/api/shipping/calculate');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['destination_city'=>'Kediri (KABUPATEN)', 'items'=>[['product_id'=>1,'quantity'=>1]]]));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
echo curl_exec($ch);
