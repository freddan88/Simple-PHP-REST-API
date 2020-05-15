<?php

declare(strict_types=1);
require('./app.conf.php');

function endSession()
{
    session_destroy();
    exit;
}

function generateToken()
{
    $_SESSION['csrf'] = md5(uniqid());
    $json = json_encode($_SESSION);
    echo $json;
}

function validateSendMail()
{
    $json = json_encode($_SESSION);
    echo $json;
    endSession();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (!isset($_POST['origin']) || empty($_POST['origin'])) exit;
    if (!isset($_POST['apikey']) || empty($_POST['apikey'])) exit;
    if ($_POST['origin'] !== $ALLOWED_ORIGIN) exit;
    if ($_POST['apikey'] !== $VALID_API_KEY) exit;
    $req = $_SERVER['REQUEST_URI'];
    session_start();

    // Route: Generate new csrf-token
    if ($req === "/token") generateToken();

    // Route: Validate security and send email
    if ($req === "/mail") validateSendMail();

    // Route: Destroy active session
    if ($req === "/session") endSession();
}

if (isset($_POST['origin'], $_POST['csrf'])) {
    $origin = $_POST['origin'];
    $token = $_POST['csrf'];
    if ($origin === 'localhost:8000' && $token === $_SESSION['csrf']) {
        $json = json_encode($_SESSION);
        echo $json;
    }
}

$email_to = trim(filter_var($_POST['email_to'], FILTER_SANITIZE_EMAIL));
$email_from = trim(filter_var($_POST['email_from'], FILTER_SANITIZE_EMAIL));
$fullname = trim(filter_var($_POST['fullname'], FILTER_SANITIZE_STRING));
$message = trim(filter_var($_POST['message'], FILTER_SANITIZE_STRING));
