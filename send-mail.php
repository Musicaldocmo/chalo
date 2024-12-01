<?php
// Prevent direct access to this file
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 403 Forbidden');
    exit('Direct access forbidden');
}

// Configuration
$recipientEmail = "mohsinadiyaan2@gmail.com"; // Replace with your email address
$emailSubject = "New Contact Form Submission - Travel Adventures";

// Sanitize and validate input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Get and sanitize form data
$name = sanitizeInput($_POST['name'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$subject = sanitizeInput($_POST['subject'] ?? '');
$message = sanitizeInput($_POST['message'] ?? '');

// Validate data
$errors = [];

if (empty($name) || strlen($name) < 2) {
    $errors[] = "Name is required and must be at least 2 characters long.";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "A valid email address is required.";
}

if (empty($subject) || strlen($subject) < 5) {
    $errors[] = "Subject is required and must be at least 5 characters long.";
}

if (empty($message) || strlen($message) < 10) {
    $errors[] = "Message is required and must be at least 10 characters long.";
}

// If there are validation errors, return them
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $errors
    ]);
    exit;
}

// Prepare email content
$emailBody = "You have received a new message from your travel website contact form.\n\n";
$emailBody .= "Name: " . $name . "\n";
$emailBody .= "Email: " . $email . "\n";
$emailBody .= "Subject: " . $subject . "\n\n";
$emailBody .= "Message:\n" . $message . "\n";

// Email headers
$headers = [
    'From' => $email,
    'Reply-To' => $email,
    'X-Mailer' => 'PHP/' . phpversion(),
    'Content-Type' => 'text/plain; charset=utf-8'
];

// Additional security headers
$headers[] = 'X-Content-Type-Options: nosniff';
$headers[] = 'X-Frame-Options: DENY';
$headers[] = 'X-XSS-Protection: 1; mode=block';

try {
    // Log the submission attempt (optional but recommended)
    error_log("Contact form submission from: " . $email);

    // Send email
    $mailSent = mail($recipientEmail, $emailSubject, $emailBody, $headers);

    if ($mailSent) {
        // Success response
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for your message. We will contact you soon!'
        ]);

        // Log success
        error_log("Contact form email sent successfully to: " . $recipientEmail);
    } else {
        throw new Exception("Failed to send email");
    }
} catch (Exception $e) {
    // Log the error
    error_log("Contact form error: " . $e->getMessage());

    // Error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Sorry, there was an error sending your message. Please try again later.'
    ]);
}

// Prevent any additional output
exit;
?>