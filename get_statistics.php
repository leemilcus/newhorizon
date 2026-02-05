<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$response = [
    'success' => false,
    'message' => 'Failed to load statistics',
    'statistics' => null
];

try {
    $statsFile = 'statistics.json';
    
    if (file_exists($statsFile)) {
        $statsData = file_get_contents($statsFile);
        $statistics = json_decode($statsData, true);
        
        if ($statistics) {
            $response['success'] = true;
            $response['message'] = 'Statistics loaded successfully';
            $response['statistics'] = $statistics;
        } else {
            $response['statistics'] = [
                'projects' => 1459,
                'clients' => 900,
                'experience' => 12
            ];
            $response['success'] = true;
        }
    } else {
        $response['statistics'] = [
            'projects' => 1459,
            'clients' => 900,
            'experience' => 12
        ];
        $response['success'] = true;
        $response['message'] = 'Using default statistics';
    }

} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>
