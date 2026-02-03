<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$galleryFile = 'gallery.json';

if (file_exists($galleryFile)) {
    $galleryData = json_decode(file_get_contents($galleryFile), true);
    if (!is_array($galleryData)) {
        $galleryData = [];
    }
} else {
    $galleryData = [];
}

echo json_encode($galleryData);
?>
