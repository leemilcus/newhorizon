<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$response = [
    'success' => false,
    'message' => 'Failed to load gallery data',
    'manualProjects' => [],
    'userProjects' => []
];

try {
    // Manual projects (from your img folder)
    $manualProjects = [
        [
            'id' => 'manual-1',
            'title' => 'Tree Removal Project',
            'description' => 'Professional tree removal service',
            'image' => './img/5.jpeg',
            'isManual' => true,
            'filename' => '5.jpeg'
        ],
        [
            'id' => 'manual-2',
            'title' => 'Stump Grinding Work',
            'description' => 'Complete stump removal service',
            'image' => './img/6.jpeg',
            'isManual' => true,
            'filename' => '6.jpeg'
        ],
        [
            'id' => 'manual-3',
            'title' => 'Tree Trimming Service',
            'description' => 'Expert tree trimming and pruning',
            'image' => './img/7.jpeg',
            'isManual' => true,
            'filename' => '7.jpeg'
        ],
        [
            'id' => 'manual-4',
            'title' => 'Site Clearing Project',
            'description' => 'Large-scale site clearing',
            'image' => './img/8.jpeg',
            'isManual' => true,
            'filename' => '8.jpeg'
        ],
        [
            'id' => 'manual-5',
            'title' => 'Garden Cleanup',
            'description' => 'Complete garden maintenance',
            'image' => './img/9.jpeg',
            'isManual' => true,
            'filename' => '9.jpeg'
        ],
        [
            'id' => 'manual-6',
            'title' => 'Palm Tree Maintenance',
            'description' => 'Specialized palm tree care',
            'image' => './img/10.jpeg',
            'isManual' => true,
            'filename' => '10.jpeg'
        ]
    ];

    // User projects (from JSON file)
    $userProjectsFile = 'user_projects.json';
    $userProjects = [];
    
    if (file_exists($userProjectsFile)) {
        $userProjectsData = file_get_contents($userProjectsFile);
        $userProjects = json_decode($userProjectsData, true) ?: [];
    }

    $response['success'] = true;
    $response['message'] = 'Gallery data loaded successfully';
    $response['manualProjects'] = $manualProjects;
    $response['userProjects'] = $userProjects;

} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>
