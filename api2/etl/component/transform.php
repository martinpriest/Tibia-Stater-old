<?php
declare(strict_types=1);

$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root . '/api2/libs/simpleHtmlDOM/simple_html_dom.php');
require_once($root . '/api2/config/config.php');
require_once($root . '/api2/etl/component/file.php');

interface Transformable {
    public function transform();
}

class TransformProcess {
    protected $inputFile;
    protected $outputFile;

    public function __construct(File $inputFile) {
        $this->inputFile = $inputFile;
    }
    // get & set input file
    public function setInputFile(File $file) {
        $this->inputFile = $file;
        return $this;
    }
    public function getInputFile() : File {
        return $this->inputFile;
    }
    // get & set output file
    public function setOutputFile(File $file) {
        $this->outputFile = $file;
        return $this;
    }
    public function getOutputFile() : File {
        return $this->outputFile;
    }

    // operating on input file
    public function openInputFile() {}
    public function deleteInputFile() {}
    // operating on file content
    public function cutFileByRegex(string $regexp) {
        $this->getInputFile()->setFileContent(preg_replace($regexp, '', $this->getInputFile()->getFileContent()));
    }
    public function selectDOM() {}
    public function stringToId() {}
    // operating on output file
    public function openOutputFile() {}
    public function saveOutputFile() {}
    // abstract
    public function transform() {}
}

class TransformWorldList extends TransformProcess {}
class TransformOnlineList extends TransformProcess {}
class TransformHighscoreList extends TransformProcess {}
class TransformPlayer extends TransformProcess {}

$inputFile = new File("liptooon");
$inputFile->read(AppConfig::$DATA_PATH . '/worldList.html');


$testTransform = new TransformProcess($inputFile);
$testTransform->cutFileByRegex("#(.*?)\"Border_3\">#is");
echo $testTransform->getInputFile()->getFileContent();