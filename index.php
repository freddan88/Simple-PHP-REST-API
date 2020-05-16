<?php

declare(strict_types=1);
require('./app.conf.php');

function addError($field)
{
    array_push($_SESSION['errors'], $field . " is missing");
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
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
    echo json_encode($_SESSION['csrf']);
}

function validateSendMail()
{
    session_start();
    if (!isset($_POST['csrf']) || empty($_POST['csrf'])) endSession();
    if ($_POST['csrf'] !== $_SESSION['csrf']) endSession();
    $_SESSION['errors'] = [];

    if (!isset($_POST['email_to']) || empty($_POST['email_to'])) endSession();
    if (!isset($_POST['message']) || empty($_POST['message'])) addError("message");
    if (!isset($_POST['fullname']) || empty($_POST['fullname'])) addError("fullname");
    if (!isset($_POST['email_from']) || empty($_POST['email_from'])) addError("email_from");

    if (count($_SESSION['errors']) > 0) {
        echo json_encode($_SESSION['errors']);
        endSession();
    }

    $origin = trim(filter_var($_POST['origin'], FILTER_SANITIZE_STRING));
    $message = trim(filter_var($_POST['message'], FILTER_SANITIZE_STRING));
    $email_to = trim(filter_var($_POST['email_to'], FILTER_SANITIZE_EMAIL));
    $fullname = trim(filter_var($_POST['fullname'], FILTER_SANITIZE_STRING));
    $email_from = trim(filter_var($_POST['email_from'], FILTER_SANITIZE_EMAIL));
    $body = $message . "\n\n" . "From: " . $fullname . "<" . $email_from . ">";
    $body = wordwrap($body, 80);

    $status = mail($email_to, "New message from: " . $origin, $body);

    if ($status === false) {
        $json = [
            "message" => "Failed to send message",
            "success" => false
        ];
        echo json_encode($json);
        endSession();
    }
    $json = [
        "message" => "Successfully sent message",
        "success" => true
    ];
    echo json_encode($json);
    endSession();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (!isset($_POST['origin']) || empty($_POST['origin'])) exit;
    if (!isset($_POST['apikey']) || empty($_POST['apikey'])) exit;
    if ($_POST['origin'] !== $ALLOWED_ORIGIN) exit;
    if ($_POST['apikey'] !== $VALID_API_KEY) exit;
    $req = $_SERVER['REQUEST_URI'];

    // Route: Generate new csrf-token
    if ($req === "/token") generateToken();

    // Route: Validate security and send email
    if ($req === "/mail") validateSendMail();

    // Route: Destroy active session
    if ($req === "/session") endSession();
}
