<?php
declare(strict_types=1);

$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root . '/api2/config/config.php');

class File {
    private $fileDirectoryPath;
    private $fileName;
    private $fileExtension;
    private $fileContent;

    public function __construct(string $fileName = "") {
        $this->setFileName($fileName);
    }

    // get & set fileDirectoryPath
    public function setFileDirectoryPath(string $fileDirectoryPath) : self {
        $this->fileDirectoryPath = $fileDirectoryPath;
        return $this;
    }
    public function getFileDirectoryPath() : string {
        return $this->fileDirectoryPath;
    }
    // get & set fileName
    public function setFileName(string $fileName) : self {
        $this->fileName = $fileName;
        return $this;
    }
    public function getFileName() : string {
        return $this->fileName;
    }
    // get & set fileExtension
    public function setFileExtension(string $fileExtension) : self {
        $this->fileExtension = $fileExtension;
        return $this;
    }
    public function getFileExtension() : string {
        return $this->fileExtension;
    }
    // get & set file content
    public function setFileContent(string $fileContent) : self {
        $this->fileContent = $fileContent;
        return $this;
    }
    public function getFileContent() : string {
        return $this->fileContent;
    }
    // get full path
    private function getFullPath() : string {
        return "{$this->fileDirectoryPath}/{$this->fileName}.{$this->fileExtension}";
    }

    // file default operation
    public function create() {
        file_put_contents($this->getFullPath(), $this->getFileContent());
    }
    public function read(string $path) {
        $this->setFileContent(
            file_get_contents($path)
        );
    }
    public function delete() {}
}

class JSON_File extends File {}
class CSV_File extends File {}
class TXT_File extends File {}
class DOM_File extends File {
    public function __construct(string $filePath, string $fileName) {
        $this->setFileExtension("html");
        $this->setFileName($fileName);
        $this->setFileDirectoryPath($filePath);
    }
}
