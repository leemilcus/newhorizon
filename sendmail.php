<?php
// sendmail.php - Contact Form Handler
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Honeypot check
    if (!empty($_POST['website'])) {
        die('Spam detected');
    }
    
    // Get form data
    $firstName = htmlspecialchars(trim($_POST['firstName']));
    $lastName = htmlspecialchars(trim($_POST['lastName']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $service = htmlspecialchars(trim($_POST['service']));
    $message = htmlspecialchars(trim($_POST['message']));
    
    // Validation
    $errors = [];
    
    if (empty($firstName)) $errors[] = "First name is required";
    if (empty($lastName)) $errors[] = "Last name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (empty($message)) $errors[] = "Message is required";
    
    if (!empty($errors)) {
        echo implode("<br>", $errors);
        exit;
    }
    
    // Email settings
    $to = "info@shailendralm.co.za"; // Change to your email
    $subject = "New Contact Form Submission - New Horizon Landscaping";
    
    // Email content
    $emailContent = "New Contact Form Submission\n\n";
    $emailContent .= "Name: $firstName $lastName\n";
    $emailContent .= "Email: $email\n";
    $emailContent .= "Phone: $phone\n";
    $emailContent .= "Service Interested In: $service\n\n";
    $emailContent .= "Message:\n$message\n\n";
    $emailContent .= "Sent from: " . $_SERVER['HTTP_HOST'];
    
    // Email headers
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Send email
    if (mail($to, $subject, $emailContent, $headers)) {
        // Also send confirmation to user
        $userSubject = "Thank you for contacting New Horizon Landscaping";
        $userMessage = "Dear $firstName,\n\nThank you for contacting New Horizon Landscaping Solutions. We have received your message and will get back to you within 24 hours.\n\nBest regards,\nNew Horizon Landscaping Team";
        
        mail($email, $userSubject, $userMessage, "From: $to");
        
        echo "success";
    } else {
        echo "Error sending email. Please try again or contact us directly.";
    }
} else {
    echo "Invalid request method.";
}
?>
