<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$response = [
    'success' => false,
    'message' => 'Unknown error'
];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid input data');
    }

    $projectId = $input['id'] ?? '';
    $imageUrl = $input['imageUrl'] ?? '';
    $isManual = $input['isManual'] ?? false;
    $filename = $input['filename'] ?? '';

    if (empty($projectId)) {
        throw new Exception('Project ID is required');
    }

    if (!$isManual) {
        // Delete user project
        $userProjectsFile = 'user_projects.json';
        
        if (!file_exists($userProjectsFile)) {
            throw new Exception('User projects file not found');
        }

        $userProjectsData = file_get_contents($userProjectsFile);
        $userProjects = json_decode($userProjectsData, true) ?: [];

        // Find the project to get filename
        $projectToDelete = null;
        foreach ($userProjects as $project) {
            if ($project['id'] === $projectId) {
                $projectToDelete = $project;
                break;
            }
        }

        // Remove the project from array
        $filteredProjects = array_filter($userProjects, function($project) use ($projectId) {
            return $project['id'] !== $projectId;
        });

        // Re-index array
        $filteredProjects = array_values($filteredProjects);

        // Delete the image file from img folder
        if ($projectToDelete && isset($projectToDelete['filename'])) {
            $imagePath = 'img/' . $projectToDelete['filename'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Also try to delete using the imageUrl
        if (!empty($imageUrl)) {
            // Extract filename from URL
            $pathParts = explode('/', $imageUrl);
            $imgFilename = end($pathParts);
            $imgPath = 'img/' . $imgFilename;
            
            if (file_exists($imgPath)) {
                unlink($imgPath);
            }
        }

        // Save updated projects
        file_put_contents($userProjectsFile, json_encode($filteredProjects, JSON_PRETTY_PRINT));

        $response['success'] = true;
        $response['message'] = 'Project and image file deleted successfully';

    } else {
        // For manual projects, we can't delete the original img folder files
        // but we can remove from the JSON data
        $response['success'] = true;
        $response['message'] = 'Manual project reference removed';
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
