<?php
ini_set('memory_limit', '256M');
require 'CodeGeneratorClass.php';

if (isset($_POST['checkInput'])) {
    echo json_encode(
        CodeGeneratorClass::checkInputValues('numberOfCodes', $_POST['numberOfCodes']) &&
        CodeGeneratorClass::checkInputValues('lengthOfCode', $_POST['lengthOfCode'])
    );
    exit();
}

if (isset($_POST['removeFile'])) {
    CodeGeneratorClass::removeFile($_POST['removeFile']);
    exit();
}

if (isset($_POST['filename'])) {
    CodeGeneratorClass::downloadFile($_POST['filename']);
    exit();
}

if ($_SERVER['argc'] > 1) {
    $mode = 'cli';
    $args = getopt('n:l:f:', [
        'numberOfCodes:',
        'lengthOfCode:',
        'file:',
    ]);

    $numberOfCodes = $args['numberOfCodes'];
    if (isset($args['n'])) {
        $numberOfCodes = $args['n'];
    }
    $lengthOfCode = $args['lengthOfCode'];
    if (isset($args['l'])) {
        $lengthOfCode = $args['l'];
    }
    $filename = $args['file'];
    if (isset($args['f'])) {
        $filename = $args['f'];
    }
} else if (!empty($_POST)) {
    $mode = 'browser';
    $numberOfCodes = $_POST['numberOfCodes'];
    $lengthOfCode = $_POST['lengthOfCode'];
    $filename = "kody_{$numberOfCodes}_{$lengthOfCode}_" . time() . ".txt";
} else {
    $man_message = "";
    $man_message .= "Usage: generateCodes.php [options]\n\n";
    $man_message .= "Options:\n";
    $man_message .= "--numberOfCodes/-n  [1 - 1000000]\n";
    $man_message .= "--lengthOfCode/-l [4 - 25]\n";
    $man_message .= "--file/-f\n";
    $man_message .= "Example:\n";
    $man_message .= "php generateCodes.php --numberOfCodes 100000 --lengthOfCode 10 --file /tmp/kody.txt\n";
    die($man_message);
}

$generator = new CodeGeneratorClass($numberOfCodes, $lengthOfCode, $filename, $mode);
$generator->generateFileWithCodes();
