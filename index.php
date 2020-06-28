<?php

declare(strict_types=1);

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: content-type");
require('./app.conf.php');

function getDatetimeNow()
{
    $tz_object = new DateTimeZone('Europe/Stockholm');
    $datetime = new DateTime();
    $datetime->setTimezone($tz_object);
    return $datetime->format('Y-m-d H:i');
}

function addError($message)
{
    array_push($_SESSION['errors'], $message);
}

function endSession()
{
    session_start();
    session_destroy();
    exit;
}

function generateToken()
{
    session_start();
    $_SESSION['csrf'] = bin2hex(random_bytes(64));
    $json = [
        "csrf" => $_SESSION['csrf']
    ];
    echo json_encode($json);
}

function validateSendMail()
{
    session_start();
    if (!isset($_POST['csrf']) || empty($_POST['csrf'])) endSession();
    if ($_POST['csrf'] !== $_SESSION['csrf']) endSession();
    $_SESSION['errors'] = [];

    if (!isset($_POST['email_to']) || empty($_POST['email_to'])) endSession();
    if (!isset($_POST['fullname']) || empty($_POST['fullname'])) addError("Name is missing");
    if (!isset($_POST['subject']) || empty($_POST['subject'])) addError("Subject is missing");
    if (!isset($_POST['message']) || empty($_POST['message'])) addError("Message is missing");
    if (!isset($_POST['email_from']) || empty($_POST['email_from'])) addError("Email is missing");

    $subject = trim(filter_var($_POST['subject'], FILTER_SANITIZE_STRING));
    $message = trim(filter_var($_POST['message'], FILTER_SANITIZE_STRING));
    $email_to = trim(filter_var($_POST['email_to'], FILTER_SANITIZE_EMAIL));
    $fullname = trim(filter_var($_POST['fullname'], FILTER_SANITIZE_STRING));
    $email_from = trim(filter_var($_POST['email_from'], FILTER_SANITIZE_EMAIL));

    if (!filter_var($email_from, FILTER_VALIDATE_EMAIL)) addError("Not a valid email address");

    if (count($_SESSION['errors']) > 0) {
        $json = [
            "messages" => $_SESSION['errors'],
            "success" => false
        ];
        echo json_encode($json);
        endSession();
    }

    $headers = "From: $fullname <$email_from>\r\n" .
        "MIME-Version: 1.0" . "\r\n" .
        "Content-type: text/html; charset=UTF-8" . "\r\n";
    $message = nl2br(wordwrap($message, 80));
    $date = getDatetimeNow();

    $body = "
    <html>
    <body>
    From: $fullname<br/>
    Mail: <a href='mailto:$email_from'>$email_from</a><br/>
    Date: $date<br/>
    -----
    <br/>
    $message
    </body>
    </html>
    ";

    $status = mail($email_to, $subject, $body, $headers);

    if ($status === false) {
        $json = [
            "messages" => ["Message failed to send"],
            "success" => false
        ];
        echo json_encode($json);
        endSession();
    }
    $json = [
        "messages" => ["Message sent successfully"],
        "success" => true
    ];
    echo json_encode($json);
    endSession();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (!isset($_POST['domain']) || empty($_POST['domain'])) exit;
    if (!isset($_POST['apikey']) || empty($_POST['apikey'])) exit;
    if (in_array($_POST['domain'], $ALLOWED_DOMAINS)) {
        if ($_POST['apikey'] === $VALID_API_KEY) {
            $uri = $_SERVER['REQUEST_URI'];
            $end = end(explode("/", $uri));
            $req = "/" . $end;

            // Route: Generate new csrf-token
            if ($req === "/token") generateToken();

            // Route: Validate security and send email
            if ($req === "/mail") validateSendMail();

            // Route: Destroy active session
            if ($req === "/session") endSession();
        }
    }
}
