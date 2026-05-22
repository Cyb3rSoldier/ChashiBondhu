<?php
header('Content-Type: application/json');
if(!isset($_GET['city'])) die(json_encode(['error'=>'No city']));
$city = urlencode($_GET['city']);
$apiKey = '6d6e9907e268584ac353392af1efbb03'; // REPLACE WITH YOUR ACTUAL KEY
$url = "https://api.openweathermap.org/data/2.5/weather?q={$city},BD&units=metric&appid={$apiKey}";
$data = @file_get_contents($url);
if($data === false) echo json_encode(['error'=>'API error']);
else echo $data;
?>