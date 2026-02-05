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

    if (empty($projectId)) {
        throw new Exception('Project ID is required');
    }

    if (!$isManual) {
        // Delete user project
        $userProjectsFile = 'gallery_data/user_projects.json';
        
        if (!file_exists($userProjectsFile)) {
            throw new Exception('User projects file not found');
        }

        $userProjectsData = file_get_contents($userProjectsFile);
        $userProjects = json_decode($userProjectsData, true) ?: [];

        // Find and remove the project
        $filteredProjects = array_filter($userProjects, function($project) use ($projectId) {
            return $project['id'] !== $projectId;
        });

        // Re-index array
        $filteredProjects = array_values($filteredProjects);

        // Delete the image file
        if (!empty($imageUrl) && file_exists($imageUrl) && strpos($imageUrl, 'uploads/') !== false) {
            unlink($imageUrl);
        }

        // Save updated projects
        file_put_contents($userProjectsFile, json_encode($filteredProjects, JSON_PRETTY_PRINT));

        $response['success'] = true;
        $response['message'] = 'Project deleted successfully';

    } else {
        // For manual projects, we just remove from session/local storage
        // (Server-side manual projects are hardcoded)
        $response['success'] = true;
        $response['message'] = 'Manual project reference removed';
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
