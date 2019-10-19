<?php

namespace Classes;

use RangeException;

class UniqueCodeGenerator
{
    const constructValidation = [
        'numberOfCodes' => [
            'options' => [
                'min_range' => 1,
                'max_range' => 1000000
            ]
        ],
        'lengthOfCode' => [
            'options' => [
                'min_range' => 4,
                'max_range' => 25
            ]
        ]
    ];

    const characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    private $numberOfCodes;
    private $lengthOfCode;
    private $uniqueCodeCounter = 0;
    private $codes = [];

    public function __construct(int $numberOfCodes, int $lengthOfCode)
    {
        if (
            !filter_var($numberOfCodes, FILTER_VALIDATE_INT, self::constructValidation['numberOfCodes']) ||
            !filter_var($lengthOfCode, FILTER_VALIDATE_INT, self::constructValidation['lengthOfCode'])
        ) {
            throw new RangeException('Incorrect values passed to UniqueCodeGenerator constructor');
        }
        $this->numberOfCodes = $numberOfCodes;
        $this->lengthOfCode = $lengthOfCode;
    }

    /**
     * @return array
     */
    public function getCodes(): array
    {
        $this->generateCodesArray();
        $this->codes = array_unique($this->codes);
        $this->uniqueCodeCounter = count($this->codes);
        if ($this->uniqueCodeCounter < $this->numberOfCodes) {
            return $this->getCodes();
        }
        return $this->codes;
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
        return true;
    }

    /**
     * Generate code that has at least one lowercase letter, uppercase letter and one digit
     * @param int $length
     * @return bool|string
     */
    private function generateRandomString(int $length): string
    {
        $code = substr(str_shuffle(self::characters), 0, $length);
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
}