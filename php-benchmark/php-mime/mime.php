
<?php

$png = "1.png.docx";
$jpg = "1.jpg.gz";
$gif = "1.gif.zip";


$png = "1.png";
$jpg = "1.jpg";
$gif = "1.gif";

// preload the file

file_get_contents($png);
file_get_contents($jpg);
file_get_contents($gif);

function minimime($fname)
{
    $fh = fopen($fname, 'rb');
    if ($fh) {
        $bytes6 = fread($fh, 6);
        fclose($fh);
        if ($bytes6 === false) return false;
        if (substr($bytes6, 0, 3) == "\xff\xd8\xff") return 'image/jpeg';
        if ($bytes6 == "\x89PNG\x0d\x0a") return 'image/png';
        if ($bytes6 == "GIF87a" || $bytes6 == "GIF89a") return 'image/gif';
        return 'application/octet-stream';
    }
    return false;
}


function minimime_($fname)
{
    $bytes6 = file_get_contents($fname,false,null,0,6);
    if ($bytes6) {
        if ($bytes6 === false) return false;
        if (substr($bytes6, 0, 3) == "\xff\xd8\xff") return 'image/jpeg';
        if ($bytes6 == "\x89PNG\x0d\x0a") return 'image/png';
        if ($bytes6 == "GIF87a" || $bytes6 == "GIF89a") return 'image/gif';
        return 'application/octet-stream';
    }
    return false;
}


$time = microtime(true);
for ($i = 200; $i--;) {
    getimagesize($png);
    getimagesize($jpg);
    $r = getimagesize($gif);
}
var_dump($r);
echo ("getimagesize took " . (microtime(true) - $time) . " seconds." . PHP_EOL);

$time = microtime(true);
for ($i = 200; $i--;) {
    exif_imagetype($png);
    exif_imagetype($jpg);
    $r = exif_imagetype($gif);
}
var_dump($r);
echo ("exif_imagetype took " . (microtime(true) - $time) . " seconds." . PHP_EOL);

$time = microtime(true);
for ($i = 200; $i--;) {
    mime_content_type($png);
    mime_content_type($jpg);
    $r = mime_content_type($gif);
}
var_dump($r);
echo ("mime_content_type took " . (microtime(true) - $time) . " seconds." . PHP_EOL);


$time = microtime(true);
for ($i = 200; $i--;) {
    minimime($png);
    minimime($jpg);
    $r = minimime($gif);
}
var_dump($r);
echo ("minimime took " . (microtime(true) - $time) . " seconds." . PHP_EOL);
