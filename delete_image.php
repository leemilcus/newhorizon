<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$response = array('success' => false, 'message' => '');
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id'])) {
    $galleryFile = 'gallery.json';
    
    if (file_exists($galleryFile)) {
        $galleryData = json_decode(file_get_contents($galleryFile), true);
        
        if (is_array($galleryData)) {
            // Find and remove the image
            $newGalleryData = [];
            $fileNameToDelete = '';
            
            foreach ($galleryData as $image) {
                if ($image['id'] != $data['id']) {
                    $newGalleryData[] = $image;
                } else {
                    $fileNameToDelete = $image['fileName'] ?? '';
                }
            }
            
            // Save updated gallery
            file_put_contents($galleryFile, json_encode($newGalleryData, JSON_PRETTY_PRINT));
            
            // Delete the image file
            if ($fileNameToDelete && file_exists('img/' . $fileNameToDelete)) {
                unlink('img/' . $fileNameToDelete);
            }
            
            $response['success'] = true;
            $response['message'] = 'Image deleted successfully!';
        }
    }
} else {
    $response['message'] = 'No image ID provided.';
}

echo json_encode($response);
?>
