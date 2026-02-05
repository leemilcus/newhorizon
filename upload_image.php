<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Create uploads directory if it doesn't exist
$uploadsDir = 'uploads/';
if (!file_exists($uploadsDir)) {
    mkdir($uploadsDir, 0777, true);
}

// Create gallery_data directory for JSON storage
$dataDir = 'gallery_data/';
if (!file_exists($dataDir)) {
    mkdir($dataDir, 0777, true);
}

$response = [
    'success' => false,
    'message' => 'Unknown error',
    'project' => null
];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Check if file was uploaded
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No image file uploaded or upload error');
    }

    // Validate file
    $imageFile = $_FILES['image'];
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if (!in_array($imageFile['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.');
    }

    if ($imageFile['size'] > $maxSize) {
        throw new Exception('File size exceeds 5MB limit.');
    }

    // Get form data
    $title = isset($_POST['title']) ? trim($_POST['title']) : 'Untitled Project';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';

    // Generate unique filename
    $fileExtension = pathinfo($imageFile['name'], PATHINFO_EXTENSION);
    $uniqueFilename = 'user_' . time() . '_' . uniqid() . '.' . strtolower($fileExtension);
    $uploadPath = $uploadsDir . $uniqueFilename;

    // Move uploaded file
    if (!move_uploaded_file($imageFile['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to save uploaded file.');
    }

    // Create project data
    $projectId = 'user_' . uniqid();
    $project = [
        'id' => $projectId,
        'title' => $title,
        'description' => $description,
        'image' => $uploadPath,
        'isManual' => false,
        'uploadDate' => date('Y-m-d H:i:s')
    ];

    // Load existing user projects
    $userProjectsFile = $dataDir . 'user_projects.json';
    $userProjects = [];
    
    if (file_exists($userProjectsFile)) {
        $existingData = file_get_contents($userProjectsFile);
        $userProjects = json_decode($existingData, true) ?: [];
    }

    // Add new project
    $userProjects[] = $project;

    // Save back to file
    file_put_contents($userProjectsFile, json_encode($userProjects, JSON_PRETTY_PRINT));

    // Return success response
    $response['success'] = true;
    $response['message'] = 'Image uploaded successfully!';
    $response['project'] = $project;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
