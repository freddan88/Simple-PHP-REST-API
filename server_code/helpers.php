<?php

declare(strict_types=1);

function getRemote()
{
    if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
        return $_SERVER['HTTP_ORIGIN'];
    }
    if (array_key_exists('HTTP_REFERER', $_SERVER)) {
        return $_SERVER['HTTP_REFERER'];
    }
    if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
        return $_SERVER['REMOTE_ADDR'];
    }
    return '';
}

function getDatetimeNow()
{
    $tz_object = new DateTimeZone('Europe/Stockholm');
    $datetime = new DateTime();
    $datetime->setTimezone($tz_object);
    return $datetime->format('Y-m-d H:i');
}

function addError($message, $field)
{
    $_SESSION['errors']->$field = $message;
}

function fieldError($message, $field, $object)
{
    $object->$field = $message;
}
