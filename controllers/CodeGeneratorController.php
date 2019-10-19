<?php
namespace Controllers;
require __DIR__ . '/../vendor/autoload.php';

use Classes\DisplayExceptionError;
use Classes\UniqueCodeGenerator as UniqueCodeGenerator;
use Classes\FileHandler as FileHandler;
use RangeException, LogicException;


class CodeGeneratorController {
    private $fileHandler;
    private $generator;

    const supportedModes = [
        'browser',
        'cli'
    ];
    const serverPath = '/tmp/';

    public function __construct(int $numberOfCodes, int $lengthOfCode, string $filename, string $mode)
    {
        if (!in_array($mode, self::supportedModes)) {
            throw new LogicException("Mode {$mode} not supported!");
        }

        if ($mode === 'browser') {
            $filename = self::serverPath . $filename;
        }
        $this->fileHandler = new FileHandler($filename);
        try {
            $this->generator = new UniqueCodeGenerator($numberOfCodes, $lengthOfCode);
        } catch (RangeException $exception) {
            DisplayExceptionError::displayErrorMessage($exception);
        }
    }

    public function generateFileWithCodes()
    {
        try {
            $this->fileHandler->writeArrayToFile($this->generator->getCodes());
            return true;
        } catch (LogicException $exception) {
            DisplayExceptionError::displayErrorMessage($exception);
            return false;
        }
    }

    public static function checkInputValues($type, $value) {
        $validation = UniqueCodeGenerator::constructValidation;
        switch($type) {
            case 'numberOfCodes':
            case 'lengthOfCode':
                $min = $validation[$type]['options']['min_range'];
                $max = $validation[$type]['options']['max_range'];
                break;
            default:
                return false;
        }
        return ($min <= $value && $value <= $max);
    }

    /**
     * Trigger file download from server
     * If file does not exist or it's name does not start with 'kody' -> redirect to main page
     * @param string $filename
     */
    public static function downloadFile(string $filename) {
        $filePath = self::serverPath . $filename;
        if (file_exists($filePath) && preg_match('%^kody%si', $filename)) {
            header('Content-type: text/plain');
            header("Content-Disposition: attachment; filename=". $filename ."");
            readfile($filePath);
        } else {
            header("Location: index.html");
            return;
        }
    }
//
    /**
     * Remove old files with codes that are no longer downloadable
     * @param string $filename
     */
    public static function removeFile(string $filename) {
        $filePath = self::serverPath . $filename;
        if (file_exists($filePath) && preg_match('%^kody%si', $filename)) {
            unlink($filePath);
            echo json_encode(["message" => "{$filename} removed."]);
        } else {
            echo json_encode(["message" => "No such file or cannot be removed."]);
        }
    }
}

