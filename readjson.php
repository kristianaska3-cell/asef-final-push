<?php

$filename = 'C:\\xampp\\htdocs\\setting.json';

 

if (!file_exists($filename)) {

    die("File not found: $filename\n");

}

 

$data = file_get_contents($filename);

$json = json_decode($data, true);

 

print_r($json);

?>