<?php

namespace Classes;
use Exception;

class DisplayExceptionError
{
    public static function displayErrorMessage(Exception $exception) {
        die("Error: {$exception->getMessage()}\nFile: {$exception->getTrace()[0]['file']} on line {$exception->getTrace()[0]['line']}");
    }
}