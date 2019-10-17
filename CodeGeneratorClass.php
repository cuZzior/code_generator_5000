<?php

class CodeGeneratorClass
{
    const numberOfCodesMin = 1;
    const numberOfCodesMax = 1000000;
    const lengthOfCodeMin = 4;
    const lengthOfCodeMax = 25;
    const serverFileDirectory = '/tmp/';
    const allowedCharacters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    private $numberOfCodes;
    private $lengthOfCode;
    private $filename;
    private $mode;
    private $uniqueCodeCounter = 0;
    private $codes = [];


    /**
     * CodeGeneratorClass constructor.
     * @param int $numberOfCodes
     * @param int $lengthOfCode
     * @param string $filename
     * @param string $mode
     */
    public function __construct(int $numberOfCodes, int $lengthOfCode, string $filename, string $mode)
    {
        $this->numberOfCodes = $numberOfCodes;
        $this->lengthOfCode = $lengthOfCode;
        $this->filename = $filename;
        $this->mode = $mode;
    }

    /**
     * Generate file with unique codes from array and save it on server
     */
    public function generateFileWithCodes() {
        if (self::checkInputValues(
            'numberOfCodes', $this->numberOfCodes) && self::checkInputValues('lengthOfCode', $this->lengthOfCode)
        ) {
            $this->generateCodesArray();
            $this->codes = array_unique($this->codes);
            $this->uniqueCodeCounter = count($this->codes);
            if ($this->uniqueCodeCounter < $this->numberOfCodes) {
                return $this->generateFileWithCodes();
            }
            $file = $this->mode !== 'cli' ? self::generateFilePath($this->filename) : $this->filename;
            if (!$handle = fopen($file, 'w')) {
                echo "Cannot open file ({$this->filename})";
                exit;
            }
            fwrite($handle, implode(PHP_EOL, $this->codes));
            fclose($handle);
            if ($this->mode === 'browser') {
                echo json_encode(
                    [
                        'success' => true,
                        'filename' => $this->filename
                    ]
                );
            }
            return true;
        } else {
            exit("Please input correct values.");
        }
    }

    /**
     * Populate class property 'codes' according to user input
     */
    private function generateCodesArray()
    {
        while ($this->uniqueCodeCounter < $this->numberOfCodes) {
            array_push($this->codes, $this->generateRandomString($this->lengthOfCode));
            $this->uniqueCodeCounter++;
        }
    }

    /**
     * Generate code that has at least one lowercase letter, uppercase letter and one digit
     * @param int $length
     * @return bool|string
     */
    private function generateRandomString(int $length) {
        $code = substr(str_shuffle(self::allowedCharacters), 0, $length);
        if (
            preg_match('%[a-z]%', $code) &&
            preg_match('%[A-Z]%', $code) &&
            preg_match('%[0-9]%', $code)
            )
        {
            return $code;
        }
        return $this->generateRandomString($length);
    }

    /**
     * Trigger file download from server
     * If file does not exist or it's name does not start with 'kody' -> redirect to main page
     * @param string $filename
     */
    public static function downloadFile(string $filename) {
        $filepath = self::generateFilePath($filename);
        if (file_exists($filepath) && preg_match('%^kody%si', $filename)) {
            header('Content-type: text/plain');
            header("Content-Disposition: attachment; filename=". $filename ."");
            readfile(self::generateFilePath($filename));
        } else {
            header("Location: index.html");
            return;
        }
    }

    /**
     * Remove old files with codes that are no longer downloadable
     * @param string $filename
     */
    public static function removeFile(string $filename) {
        $filepath = self::generateFilePath($filename);
        if (file_exists($filepath) && preg_match('%^kody%si', $filename)) {
            unlink(self::generateFilePath($filename));
            echo json_encode(["message" => "{$filename} removed."]);
        } else {
            echo json_encode(["message" => "No such file or cannot be removed."]);
        }
    }

    /**
     * Validate user input
     * @param $type
     * @param $value
     * @return bool
     */
    public static function checkInputValues($type, $value) {
        switch($type) {
            case 'numberOfCodes':
                $min = self::numberOfCodesMin;
                $max = self::numberOfCodesMax;
                break;
            case 'lengthOfCode':
                $min = self::lengthOfCodeMin;
                $max = self::lengthOfCodeMax;
                break;
            default:
                return false;
        }
        return ($min <= $value && $value <= $max);
    }

    /**
     * Generate path to file
     * @param string $filename
     * @return string
     */
    private static function generateFilePath(string $filename) {
        return self::serverFileDirectory . $filename;
    }
}
