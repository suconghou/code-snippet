<?php

$data = "hi";

$key = "1234567890123456";
$iv = '1234567890123456';
var_dump(base64_encode(openssl_encrypt($data, "AES-128-CFB", $key, OPENSSL_RAW_DATA,  $iv)));
