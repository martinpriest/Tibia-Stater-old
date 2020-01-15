<?php

/**
 * Description: Class for extracting process in ETL.
 * Issues: check regex for setURL, autoloader for libraries, use curl_multi for faster getting html pages, libevent
 */
$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root . '/api/libs/simpleHtmlDOM/simple_html_dom.php');

class Extract {
    // Class parameters
    private $conn;
    private $url;
    private $fileCreatedCounter;
    private $fileModifiedCounter;
    private $executionTime;

    // Primary constructor
    public function __construct() {
        $url = "";
        $fileCreatedCounter = 0;
        $fileModifiedCounter = 0;
        $executionTime = 0;
    }

    // Getters and setters for class parameters
    // Parameter: url
    public function getURL() : string {
        return $this->url;
    }
    public function setURL(string $url) : self {
        if(is_string($url)) {
            $this->url = $url;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Error found at class: " . __CLASS__ . "\nCan't set URL. {$url} is not a string.")));
        }
    }

    // Parameter: fileCreatedCounter
    public function getFileCreatedCounter() : int {
        return $this->fileCreatedCounter;
    }
    public function setFileCreatedCounter(int $fileCreatedCounter) : self {
        if(is_int($fileCreatedCounter)) {
            $this->fileCreatedCounter = $fileCreatedCounter;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Error found at class: " . __CLASS__ . "\nCan't set variable fileCreatedCounter. ")));
        }
    }

    // Parameter: fileModifiedCounter
    public function getFileModifiedCounter() : int {
        return $this->fileModifiedCounter;
    }
    public function setFileModifiedCounter(int $fileModifiedCounter) : self {
        if(is_int($fileModifiedCounter)) {
            $this->fileModifiedCounter = $fileModifiedCounter;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Error found at class: " . __CLASS__ . "\nCan't set variable fileCreatedCounter. ")));
        }
    }

    // Parameter: executionTime
    public function getExecutionTime() : int {
        return $this->executionTime;
    }
    public function setExecutionTime(int $executionTime) : self {
        if(is_int($executionTime)) {
            $this->executionTime += $executionTime;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Error found at class: " . __CLASS__ . "\nProblem at method: " . __METHOD__)));
        }
    }

    // Private function used in public process
    private function getFileContentFromURL() : string {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($c, CURLOPT_URL, $this->url);

        curl_setopt($c, CURLOPT_HTTPHEADER, array(
            'User-Agent: PostmanRuntime/7.19.0',
            'Accept: */*',
            'Cache-Control: no-cache',
            'Host: www.tibia.com',
            'Accept-Encoding: identity',
            'Connection: keep-alive'
        ));

        $contents = curl_exec($c);
        curl_close($c);

        if ($contents) {
            $contents = preg_replace("#(.*?)\"Border_3\">#is", '', $contents);
            $contents = preg_replace("#<div class=\"Border_1\"(.*?)</html>#is", '', $contents);
            return $contents;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Error found at class: " . __CLASS__ . "\nCan't get content from URL: {$this->url}")));
        }
    }

    private function makeDirTree(array $arr) : bool {
        $root = $_SERVER['DOCUMENT_ROOT'];
        $dirPath = "{$root}/api/adminApi/data/";
        if(!is_dir($dirPath)) mkdir($dirPath);
        if(empty($arr)) return false;
        foreach($arr as $el) {
            $dirPath = "{$dirPath}/{$el['name']}"; // "{$dirPath}/{$el['name']}"   '../data/' . $el['name']
            if(!is_dir($dirPath)) mkdir($dirPath);
            if(!is_dir($dirPath . '/characters')) mkdir($dirPath . '/characters');
            if(!is_dir($dirPath . '/guilds')) mkdir($dirPath . '/guilds');
            if(!is_dir($dirPath . '/highscores')) mkdir($dirPath . '/highscores');
            $highscoreTypes = array("achievements", "axe", "club", "distance", "experience", "fishing", "fist", "loyalty", "magic", "shielding", "sword");
            foreach($highscoreTypes as $type) {
                if(!is_dir("{$dirPath}/highscores/{$type}")) mkdir("{$dirPath}/highscores/{$type}");
            }
            $dirPath = "{$root}/api/adminApi/data/";
        }
        return true;
    }

    // Public interface of extracting process
    public function getServers() : iterable {
        $this->setURL("https://www.tibia.com/community/?subtopic=worlds");
        $serverFile = $this->getFileContentFromURL();

        $html = str_get_html($serverFile);
        $tableContent = $html->find('table.TableContent', 2)->find('tr.Odd, tr.Even');

        $serverList = array();

        foreach($tableContent as $el) {
            $server = array(
                'name' => utf8_encode($el->find('text', 0)),
                'status' => utf8_encode($el->find('text', 1)),
                'location' => utf8_encode($el->find('text', 2))
            );
            array_push($serverList, $server);
        }
        $this->makeDirTree($serverList);
        return $serverList;
    }

    public function extractOnlineList(string $serverName) {
        $this->setURL("https://www.tibia.com/community/?subtopic=worlds&world=" . $serverName);
        $file = $this->getFileContentFromURL();

        $root = $_SERVER['DOCUMENT_ROOT'];
        $filePath = "{$root}/api/adminApi/data/{$serverName}/characters/onlinePlayerList.html";
        if(!is_file($filePath)) touch($filePath);
        file_put_contents($filePath, $file);
        $this->fileCreatedCounter++;

        $html = str_get_html($file);
        $tableContent = $html->find('div.InnerTableContainer', 2)->find('tr.Odd, tr.Even');

        foreach($tableContent as $el) {
            $playerName = str_replace('&#160;', ' ', $el->find('text', 0));
            $this->setURL("https://www.tibia.com/community/?subtopic=characters&name=" . rawurlencode($playerName));
            $file = $this->getFileContentFromURL();
            $filePath = "{$root}/api/adminApi/data/{$serverName}/characters/{$playerName}.html";
            if(!is_file($filePath)) touch($filePath);
            file_put_contents($filePath, $file);
            $this->fileCreatedCounter++;
        }
    }

    public function extractHighscores(string $serverName) {
        $highscoreTypes = array("achievements", "axe", "club", "distance", "experience", "fishing", "fist", "loyalty", "magic", "shielding", "sword");
        $root = $_SERVER['DOCUMENT_ROOT'];
        foreach($highscoreTypes as $type) {
            for($i=1; $i<13; $i++) {
                $this->setURL("https://www.tibia.com/community/?subtopic=highscores&world={$serverName}&list={$type}&profession=0&currentpage={$i}");
                $file = $this->getFileContentFromURL();
                $filePath = "{$root}/api/adminApi/data/{$serverName}/highscores/{$type}/{$i}.html";
                if(!is_file($filePath)) touch($filePath);
                // wykrajanie czesci nieistotnych
                // if($type == "achievements" || $type == "loyalty") $file = preg_replace("#(.*?)<td style=\"width: 10%; text-align: right;\">Points</td></tr>#is", '', $file);
                // else if($type == "experience") $file = preg_replace("#(.*?)<td style=\"width: 20%; text-align: right;\">Points</td></tr>#is", '', $file);
                // else if($type == "axe" || $type == "club" || $type == "distance" || $type == "fishing" ||
                //         $type == "fist" || $type == "magic" || $type == "shielding" || $type == "sword") $file = preg_replace("#(.*?)<td style=\"width: 10%; text-align: right;\">Level</td></tr>#is", '', $file);
                    
                // $file = preg_replace("#<tr><td style=\"padding-right: 10px;(.*?)#is", '', $file);

                file_put_contents($filePath, $file);
                $this->fileCreatedCounter++;

                $html = str_get_html($file);
                $tableContent = $html->find('div.InnerTableContainer', 1)->find('tr[style="background-color:#F1E0C6;"], tr[style="background-color:#D4C0A1;"]');

                foreach($tableContent as $el) {
                    $playerName = $el->find('text', 1);
                    $this->setURL("https://www.tibia.com/community/?subtopic=characters&name=" . rawurlencode($playerName));
                    $file = $this->getFileContentFromURL();
                    //$filePath = "{$root}/api/adminApi/data/{$serverName}/highscores/{$type}/{$playerName}.html";
                    $filePath = "{$root}/api/adminApi/data/{$serverName}/characters/{$playerName}.html";
                    if(!is_file($filePath)) touch($filePath);
                    file_put_contents($filePath, $file);
                    $this->fileCreatedCounter++;
                }
            }
        }
    }
}