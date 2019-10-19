<?php

namespace Classes;
use LogicException;

class FileHandler
{
    private $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }
    public function writeArrayToFile(array $array, string $delimiter = PHP_EOL): bool
    {
        if (!$fh = fopen($this->filename, 'w')) {
            throw new LogicException("File {$this->filename} cannot be open for writing.");
        }
        fwrite($fh, implode($delimiter, $array));
        fclose($fh);
        return true;
    }
}