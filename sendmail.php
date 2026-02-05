<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$response = [
    'success' => false,
    'message' => 'Failed to send message'
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

    // Extract form data
    $firstName = htmlspecialchars($input['firstName'] ?? '');
    $lastName = htmlspecialchars($input['lastName'] ?? '');
    $phone = htmlspecialchars($input['phone'] ?? '');
    $email = filter_var($input['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $service = htmlspecialchars($input['service'] ?? '');
    $message = htmlspecialchars($input['message'] ?? '');

    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($phone) || empty($email)) {
        throw new Exception('Please fill in all required fields');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }

    // Email configuration
    $to = "info@villagefeller.co.za"; // Replace with your actual email
    $subject = "New Quote Request from Village Feller Website";
    
    // Email body
    $emailBody = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background-color: #2e7d32; color: white; padding: 20px; }
            .content { padding: 20px; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #2e7d32; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h2>New Quote Request</h2>
        </div>
        <div class='content'>
            <div class='field'>
                <span class='label'>Client Name:</span> $firstName $lastName
            </div>
            <div class='field'>
                <span class='label'>Phone:</span> $phone
            </div>
            <div class='field'>
                <span class='label'>Email:</span> $email
            </div>
            <div class='field'>
                <span class='label'>Service Required:</span> $service
            </div>
            <div class='field'>
                <span class='label'>Message:</span><br>
                $message
            </div>
            <div class='field'>
                <span class='label'>Submitted:</span> " . date('Y-m-d H:i:s') . "
            </div>
        </div>
    </body>
    </html>
    ";

    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Village Feller Website <noreply@villagefeller.co.za>" . "\r\n";
    $headers .= "Reply-To: $email" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Send email
    if (mail($to, $subject, $emailBody, $headers)) {
        $response['success'] = true;
        $response['message'] = 'Thank you! Your message has been sent successfully.';
    } else {
        throw new Exception('Failed to send email. Please try again later.');
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
