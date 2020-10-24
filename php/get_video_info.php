<?php 

$file ="/tmp/get_video_info";

$str = file_get_contents($file);

parse_str($str,$data);

// print_r($data);

echo $data['player_response'];

$res = json_decode($data['player_response'],true);
// print_r($res);