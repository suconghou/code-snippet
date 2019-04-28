
<?php

$png = "1.png";
$jpg = "1.jpg";
$gif = "1.gif";

// preload the file

file_get_contents($png);
file_get_contents($jpg);
file_get_contents($gif);

function minimime($fname) {
    $fh=fopen($fname,'rb');
    if ($fh) { 
        $bytes6=fread($fh,6);
        fclose($fh); 
        if ($bytes6===false) return false;
        if (substr($bytes6,0,3)=="\xff\xd8\xff") return 'image/jpeg';
        if ($bytes6=="\x89PNG\x0d\x0a") return 'image/png';
        if ($bytes6=="GIF87a" || $bytes6=="GIF89a") return 'image/gif';
        return 'application/octet-stream';
    }
    return false;
}

$time = microtime( true );
for( $i = 200; $i--; ) {
    getimagesize($png);
    getimagesize($jpg);
    getimagesize($gif);
}
echo( "getimagesize took " . (microtime( true ) - $time) . " seconds.".PHP_EOL );

$time = microtime( true );
for( $i = 200; $i--; ) {
    exif_imagetype($png);
    exif_imagetype($jpg);
    exif_imagetype($gif);
}
echo( "exif_imagetype took " . (microtime( true ) - $time) . " seconds.".PHP_EOL );

$time = microtime( true );
for( $i = 200; $i--; ) {
    mime_content_type($png);
    mime_content_type($jpg);
    mime_content_type($gif);
}
echo( "mime_content_type took " . (microtime( true ) - $time) . " seconds.".PHP_EOL );


$time = microtime( true );
for( $i = 200; $i--; ) {
    minimime($png);
    minimime($jpg);
    minimime($gif);
}
echo( "minimime took " . (microtime( true ) - $time) . " seconds.".PHP_EOL );
