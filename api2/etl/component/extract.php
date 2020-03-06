<?php
declare(strict_types=1);

$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root . '/api2/libs/simpleHtmlDOM/simple_html_dom.php');
require_once($root . '/api2/config/config.php');

class ExtractManager {
    private $extractStrategy;

    public function __construct() {
        $this->createDataDir();
    }

    public function setStrategy(ExtractProcess $obj) {
        $this->extractStrategy=$obj;
    }
    public function getStrategy() {
        return $this->extractStrategy;
    }

    private function createDataDir() : void {
        if(!is_dir(DATA_PATH)) mkdir(DATA_PATH);
    }
}

abstract class ExtractProcess {
    protected $extractURL;
    protected $targetDirectory;
    protected $fileName;
    protected $fileContent;
    protected $c;
    // Param: extractURL
    public function setExtractURL(string $extractURL) : self {
        $this->extractURL = $extractURL;
        return $this;
    }
    public function getExtractURL() : string {
        return $this->extractURL;
    }
    // Param: targetDirectory
    public function setTargetDirectory(string $dirPath = "") : self {
        $this->targetDirectory = DATA_PATH . $dirPath;
        return $this;
    }
    public function getTargetDirectory() : string {
        return $this->targetDirectory;
    }
    // Param: fileContent
    public function setFileContent(string $fileContent) : self {
        $this->fileContent = $fileContent;
        return $this;
    }
    public function getFileContent() : string {
        return $this->fileContent;
    }
    // Param: fileName
    public function setFileName(string $fileName) : self {
        $this->fileName = $fileName;
        return $this;
    }
    public function getFileName() : string {
        return $this->fileName;
    }

    // create directory
    public function createDirectory() : void {
        if(!is_dir($this->getTargetDirectory())) mkdir($this->getTargetDirectory());
    }

    // extracting, setting, getting, saving fileContent
    abstract public function extract();

    protected function extractFileContentFromURL() : self {
        //$c = curl_init();
        curl_setopt($this->c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->c, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->c, CURLOPT_ENCODING, '');
        curl_setopt($this->c, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($this->c, CURLOPT_TCP_FASTOPEN, 1);
        curl_setopt($this->c, CURLOPT_URL, $this->getExtractURL());

        curl_setopt($this->c, CURLOPT_HTTPHEADER, array(
            'User-Agent: PostmanRuntime/7.19.0',
            'Accept: */*',
            'Cache-Control: no-cache',
            'Host: www.tibia.com',
            'Accept-Encoding: identity',
            'Connection: keep-alive'
        ));

        $this->setFileContent(curl_exec($this->c));
        //curl_close($this->c);

        if ($this->fileContent) {
            // $this->setFileContent(preg_replace("#(.*?)\"Border_3\">#is", '', $this->getFileContent()));
            // $this->setFileContent(preg_replace("#<div class=\"Border_1\"(.*?)</html>#is", '', $this->getFileContent()));
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Error found at class: " . __CLASS__ . "\nCan't get content from URL: {$this->getExtractURL()}")));
        }
    }
    
    protected function saveFileContent() : void {
        file_put_contents("{$this->getTargetDirectory()}/{$this->getFileName()}", $this->getFileContent());
    }
}

class ExtractWorldList extends ExtractProcess {
    // Primary constructor
    public function __construct() {
        $this->setExtractURL("https://www.tibia.com/community/?subtopic=worlds");
        $this->setTargetDirectory()->createDirectory();
        $this->setFileName("worldList.html");
        $this->c = curl_init();
    }

    public function extract() {
        $this->extractFileContentFromURL()
            ->saveFileContent();
    }
}

class ExtractOnlinePlayerList extends ExtractProcess {
    private $worldName;

    public function __construct(string $worldName) {
        $this->setWorldName($worldName);
        $this->setFileName("onlineList.html");
        $this->c = curl_init();
    }

    public function setWorldName(string $worldName) : self {
        $this->worldName = $worldName;
        $this->setTargetDirectory("/{$worldName}")->createDirectory();
        $this->setExtractURL("https://www.tibia.com/community/?subtopic=worlds&world={$worldName}");
        return $this;
    }
    public function getWorldName() : string {
        return $this->worldName;
    }

    public function extract() {
        $this->extractFileContentFromURL()
            ->saveFileContent();
    }
}

class ExtractHighscoreList extends ExtractProcess {
    private $worldName;

    public function __construct(string $worldName) {
        $this->setWorldName($worldName);
        $this->c = curl_init();
    }

    public function setWorldName(string $worldName) : self {
        $this->worldName = $worldName;
        $this->setTargetDirectory("/{$worldName}/highscores")->createDirectory();
        return $this;
    }
    public function getWorldName() : string {
        return $this->worldName;
    }

    public function extract() {
        $highscoreTypes = array("achievements", "axe", "club", "distance", "experience", "fishing", "fist", "loyalty", "magic", "shielding", "sword");
        foreach($highscoreTypes as $type) {
            $this->setTargetDirectory("/{$this->getWorldName()}/highscores/{$type}")->createDirectory();
            for($i = 1; $i<13; $i++) {
                $this->setExtractURL("https://www.tibia.com/community/?subtopic=highscores&world={$this->getWorldName()}&list={$type}&profession=0&currentpage={$i}");
                $this->setFileName("{$i}.html");
                $this->extractFileContentFromURL()
                    ->saveFileContent();
            }
        }
    }
}

class ExtractPlayer extends ExtractProcess {
    private $playerName;
    private $worldName;

    public function __construct(string $worldName, string $playerName) {
        $this->setWorldName($worldName);
        $this->setPlayerName($playerName);
        $this->c = curl_init();
    }

    public function setWorldName(string $worldName) : self {
        $this->worldName = $worldName;
        $this->setTargetDirectory("/{$worldName}/players")->createDirectory();
        return $this;
    }
    public function getWorldName() : string {
        return $this->worldName;
    }

    public function setPlayerName(string $playerName) : self {
        $this->playerName = $playerName;
        return $this;
    }
    public function getPlayerName() : string {
        return $this->worldName;
    }

    public function extract() {
        $this->setExtractURL("https://www.tibia.com/community/?subtopic=characters&name=" . rawurlencode($this->getPlayerName()));
        $this->setFileName("{$this->getPlayerName()}.html");
        $this->extractFileContentFromURL()
            ->saveFileContent();
    }
}