<?php
// Allow CORS if needed
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Response array
$response = array('success' => false, 'message' => '', 'fileName' => '', 'id' => '');

// Check if image was uploaded
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    // Get uploaded file info
    $file = $_FILES['image'];
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        $response['message'] = 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.';
        echo json_encode($response);
        exit;
    }
    
    // Validate file size (5MB max)
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        $response['message'] = 'File is too large. Maximum size is 5MB.';
        echo json_encode($response);
        exit;
    }
    
    // Generate unique filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $uniqueName = uniqid() . '_' . time() . '.' . $fileExtension;
    $uploadPath = 'img/' . $uniqueName;
    
    // Ensure img directory exists
    if (!is_dir('img')) {
        mkdir('img', 0755, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // Create image data
        $imageData = [
            'id' => time(),
            'fileName' => $uniqueName,
            'title' => $title,
            'description' => $description,
            'category' => $category,
            'uploaded' => date('Y-m-d H:i:s')
        ];
        
        // Update gallery.json
        $galleryFile = 'gallery.json';
        $galleryData = [];
        
        if (file_exists($galleryFile)) {
            $galleryData = json_decode(file_get_contents($galleryFile), true);
        }
        
        if (!is_array($galleryData)) {
            $galleryData = [];
        }
        
        $galleryData[] = $imageData;
        file_put_contents($galleryFile, json_encode($galleryData, JSON_PRETTY_PRINT));
        
        $response['success'] = true;
        $response['message'] = 'Image uploaded successfully!';
        $response['fileName'] = $uniqueName;
        $response['id'] = $imageData['id'];
    } else {
        $response['message'] = 'Failed to save file.';
    }
} else {
    $response['message'] = 'No file uploaded or upload error.';
}

echo json_encode($response);
?>
