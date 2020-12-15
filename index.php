<?php

declare(strict_types=1);
require_once('./app.conf.php');
require_once('./helpers.php');
$current_origin = getRemote();

if (in_array($current_origin, $ALLOWED_ORIGINS)) {
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Origin: ' . $current_origin);
    header("Access-Control-Allow-Headers: X-PINGOTHER, Content-Type");
    header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, HEAD, OPTIONS");
} else {
    die('{}');
}

function endSession()
{
    session_destroy();
    exit;
}

function generateToken()
{
    $SESSION_ID = session_id();
    $_SESSION['csrf'] = bin2hex(random_bytes(64));
    $json = [
        "csrf" => $_SESSION['csrf'],
        "uuid" => $SESSION_ID
    ];
    echo json_encode($json);
}

function validateSendMail()
{
    if (!isset($_SESSION['csrf']) || empty($_SESSION)) endSession();
    if (!isset($_POST['csrf']) || empty($_POST['csrf'])) endSession();
    if ($_POST['csrf'] !== $_SESSION['csrf']) endSession();
    $_SESSION['errors'] = [];
    
    if (!isset($_POST['email_from']) || empty($_POST['email_from'])) endSession();
    if (!isset($_POST['name']) || empty($_POST['name'])) addError("Name is missing", "name");
    if (!isset($_POST['subject']) || empty($_POST['subject'])) addError("Subject is missing", "subject");
    if (!isset($_POST['message']) || empty($_POST['message'])) addError("Message is missing", "message");
    if (!isset($_POST['email_to']) || empty($_POST['email_to'])) addError("Email is missing", "email_to");

    $name = trim(filter_var($_POST['name'], FILTER_SANITIZE_STRING));
    $subject = trim(filter_var($_POST['subject'], FILTER_SANITIZE_STRING));
    $message = trim(filter_var($_POST['message'], FILTER_SANITIZE_STRING));
    $email_to = trim(filter_var($_POST['email_to'], FILTER_SANITIZE_EMAIL));
    $email_from = trim(filter_var($_POST['email_from'], FILTER_SANITIZE_EMAIL));
    if (!filter_var($email_to, FILTER_VALIDATE_EMAIL)) addError("Not a valid email address", "email_to");

    if (count($_SESSION['errors']) > 0) {
        $json = [
            "messages" => $_SESSION['errors'],
            "type" => "fields",
            "success" => false
        ];
        echo json_encode($json);
        endSession();
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
    Mail: <a href='mailto:$email_from'>$email_from</a><br/>
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
            "message" => "Message failed to send",
            "type" => "global",
            "success" => false
        ];
        echo json_encode($json);
        endSession();
    }
    $json = [
        "message" => "Message sent successfully",
        "type" => "global",
        "success" => true
    ];
    echo json_encode($json);
    endSession();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (!isset($_POST['apikey']) || empty($_POST['apikey'])) die('{KEY}');
    if ($_POST['apikey'] === $VALID_API_KEY) {
        $uri = $_SERVER['REQUEST_URI'];
        $exp = explode("/", $uri);
        $end = end($exp);
        $req = "/" . $end;
        if (isset($_POST['uuid'])) {
            session_id($_POST['uuid']);
        }
        session_start();

        // Route: Generate new csrf-token
        if ($req === "/token") generateToken();

        // Route: Validate security and send email
        if ($req === "/mail") validateSendMail();

        // Route: Destroy active session
        if ($req === "/end") endSession();
    }
}
