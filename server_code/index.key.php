<?php

declare(strict_types=1);
require_once('./config.php');
require_once('./helpers.php');
$current_origin = getRemote();

if (in_array($current_origin, $ALLOWED_ORIGINS)) {
    header('Access-Control-Allow-Origin: ' . $current_origin);
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: POST");
} else {
    exit;
}

function validateSendMail()
{
    $field_errors = new stdClass();

    if (!isset($_POST['email_from']) || empty($_POST['email_from'])) exit;
    if (!isset($_POST['name']) || empty($_POST['name'])) $_POST['name'] = 'Anonymous';
    if (!isset($_POST['message']) || empty($_POST['message'])) fieldError("Message is missing", "message", $field_errors);
    // if (!isset($_POST['email_to']) || empty($_POST['email_to'])) fieldError("Email is missing", "email_to", $field_errors);
    // if (!isset($_POST['subject']) || empty($_POST['subject'])) fieldError("Subject is missing", "subject", $field_errors);
    // if (!isset($_POST['name']) || empty($_POST['name'])) fieldError("Name is missing", "name", $field_errors);

    $name = trim(filter_var($_POST['name'], FILTER_SANITIZE_STRING));
    $subject = trim(filter_var($_POST['subject'], FILTER_SANITIZE_STRING));
    $message = trim(filter_var($_POST['message'], FILTER_SANITIZE_STRING));
    $email_to = trim(filter_var($_POST['email_to'], FILTER_SANITIZE_EMAIL));
    $email_from = trim(filter_var($_POST['email_from'], FILTER_SANITIZE_EMAIL));

    if (!$field_errors->email_to) {
        if (!filter_var($email_to, FILTER_VALIDATE_EMAIL)) fieldError("Not a valid email address", "email_to", $field_errors);
    }

    if (count((array)$field_errors) > 0) {
        $json = [
            'field_errors' => $field_errors
        ];
        echo json_encode($json);
        exit;
    }

    $headers = "From: $name <$email_from>\r\n" .
        "MIME-Version: 1.0" . "\r\n" .
        "Content-type: text/html; charset=UTF-8" . "\r\n";
    $message = nl2br(wordwrap($message, 80));
    $date = getDatetimeNow();

    $body = "
    <html>
    <body>
    From: $name<br/>
    Mail: <a href='mailto:$email_to'>$email_to</a><br/>
    Date: $date<br/>
    -----
    <br/>
    $message
    </body>
    </html>
    ";

    $email_status = mail($email_to, $subject, $body, $headers);

    if (!$email_status) {
        $json = [
            'global_status' => [
                "message" => "Email failed to send",
                "success" => false
            ]
        ];
        echo json_encode($json);
        exit;
    }
    $json = [
        'global_status' => [
            "message" => "Email sent successfully",
            "success" => true
        ]
    ];
    echo json_encode($json);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (!isset($_POST['apikey']) || empty($_POST['apikey'])) exit;
    if ($_POST['apikey'] === $VALID_API_KEY) {
        $uri = $_SERVER['REQUEST_URI'];
        $exp = explode("/", $uri);
        $end = end($exp);
        $req = "/" . $end;

        // Route: Validate security and send email
        if ($req === "/mail") validateSendMail();
    }
}
