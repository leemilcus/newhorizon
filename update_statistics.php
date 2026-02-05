<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$response = [
    'success' => false,
    'message' => 'Failed to update statistics'
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

    $projects = intval($input['projects'] ?? 1459);
    $clients = intval($input['clients'] ?? 900);
    $experience = intval($input['experience'] ?? 12);

    // Create statistics data
    $statistics = [
        'projects' => $projects,
        'clients' => $clients,
        'experience' => $experience,
        'lastUpdated' => date('Y-m-d H:i:s')
    ];

    // Save to file
    $statsFile = 'statistics.json';
    file_put_contents($statsFile, json_encode($statistics, JSON_PRETTY_PRINT));

    $response['success'] = true;
    $response['message'] = 'Statistics updated successfully';
    $response['statistics'] = $statistics;

} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>
