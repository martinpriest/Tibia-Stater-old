<?php

$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root . '/api/libs/simpleHtmlDOM/simple_html_dom.php');

class Transform {
    // Parameters
    private $filesReadCounter;
    private $filesCreatedCounter;

    // Constructor
    public function __construct() {
        $filesReadCounter = 0;
        $filesCreatedCounter = 0;
    }

    public function getFilesReadCounter() : int {
        return $this->filesReadCounter;
    }

    public function getFilesCreatedCounter() : int {
        return $this->filesCreatedCounter;
    }

        // jak uzytkownik nie da nazwy swiata to controller sam se odczyta cala liste i bedzie podawal :) aha i po kazdej ekstrakcji dodac zmiane stanu w bazie danych ostatniej operacji
    // Private method for public usage
    private function transformOnlinePlayers(string $worldName) {
        // pobierz nazwy wszystkich plików w folderze świata a następnie usuń dwa pierwsze elementy tablicy (./ i ../)
        $root = $_SERVER['DOCUMENT_ROOT'];
        $fileNames = scandir($root ."/api/adminApi/data/{$worldName}/characters/");
        unset($fileNames[0], $fileNames[1]);
        // utwórz tablice nazw graczy oraz dla kazdego z graczy tablice parametrow
        $playerArr = array();
        $playerNames = array();

        foreach($fileNames as $file) {
            if($file != "transformResult.json" && $file != "onlinePlayerList.html") {
                $fileContent=file_get_contents("{$root}/api/adminApi/data/{$worldName}/characters/{$file}");
                $this->filesReadCounter++;
                // Make array element
                $player = array();
                //if(preg_match('~Name:</td><td>(.*?)</td>~i', $fileContent, $temp)) $player['name'] = substr(strip_tags($temp[1]), 0, -1);
                if(preg_match('~Former Names:</td><td>(.*?)</td>~i', $fileContent, $temp)) {
                    $player['formerNames'] = array();
                    $player['formerNames'] = explode(", ", strip_tags($temp[1]));
                }
                if(preg_match('~Title:</td><td>(.*?)</td>~i', $fileContent, $temp)) $player['title'] = strip_tags($temp[1]);
                if(preg_match('~Sex:</td><td>(.*?)</td>~i', $fileContent, $temp)) $player['sex'] = strip_tags($temp[1]);
                if(preg_match('~Vocation:</td><td>(.*?)</td>~i', $fileContent, $temp)) $player['vocation'] = strip_tags($temp[1]);
                if(preg_match('~Level:</td><td>(.*?)</td>~i', $fileContent, $temp)) $player['level'] = intval(strip_tags($temp[1]));
                if(preg_match('~Achievement Points:</nobr></td><td>(.*?)</td>~i', $fileContent, $temp)) $player['achievmentPoints'] = intval(strip_tags($temp[1]));
                if(preg_match('~World:</td><td>(.*?)</td>~i', $fileContent, $temp)) $player['world'] = strip_tags($temp[1]);
                if(preg_match('~Residence:</td><td>(.*?)</td>~i', $fileContent, $temp)) $player['residence'] = strip_tags($temp[1]);
                if(preg_match('~action=characters">(.*?)is paid~i', $fileContent, $temp)) $player['house'] = str_replace('&#160;', ' ', strip_tags($temp[1]));
                if(preg_match('~Membership:</td><td>(.*?) of the~i', $fileContent, $temp)) $player['guildRank'] = strip_tags($temp[1]);
                if(preg_match('~action=characters\">(.*?)</a></td>~i', $fileContent, $temp)) $player['guild'] = str_replace('&#160;', ' ', strip_tags(preg_replace("#(.*?)<a (.*?)>#is", '', $temp[1])));
                if(preg_match('~Last Login:</td><td>(.*?)</td>~i', $fileContent, $temp)) $player['lastLogin'] = str_replace('&#160;', ' ', strip_tags($temp[1]));
                if(preg_match('~Status:</td><td>(.*?)</td>~i', $fileContent, $temp)) $player['accountType'] = strip_tags($temp[1]);
                array_push($playerNames, str_replace('.html', '', strip_tags($file)));
                array_push($playerArr, $player);
            }
        }
        //utwórz z tablic plik json
        $resultArr = array_combine($playerNames, $playerArr);
        file_put_contents("{$root}/api/adminApi/data/{$worldName}/characters.json", json_encode($resultArr));
        $this->filesCreatedCounter++;
    }

    private function transformHighscores(string $worldName) {
        $highscoreTypes = array("achievements", "axe", "club", "distance", "experience", "fishing", "fist", "loyalty", "magic", "shielding", "sword");
        $root = $_SERVER['DOCUMENT_ROOT'];

        $highscorePlayersArr = array();
        // $playerNames = array();

        foreach($highscoreTypes as $type) {
            for($i=1; $i<13; $i++) {
                $filePath = "{$root}/api/adminApi/data/{$worldName}/highscores/{$type}/{$i}.html";
                if(is_file($filePath)) {
                    $fileContent = file_get_contents($filePath);
                    $this->filesReadCounter++;
                    $html = str_get_html($fileContent);
                    $tableContent = $html->find('div.InnerTableContainer', 1)->find('tr[style="background-color:#F1E0C6;"], tr[style="background-color:#D4C0A1;"]');
    
                    foreach($tableContent as $highscoreRecord) {
                        $playerName = utf8_encode($highscoreRecord->find('text', 1));
                        $typeName = ucfirst($type);
                        if($type == "experience" || $type == "loyalty") $rankValue = intval(str_replace(',', '', utf8_encode($highscoreRecord->find('text', 4))));
                        else $rankValue = intval(str_replace(',', '', utf8_encode($highscoreRecord->find('text', 3))));
                        
                        $player = array(
                            "{$playerName}"=>array(
                                "world" => $worldName,
                                "highscore{$typeName}" => array(
                                    "rankPosition" => utf8_encode($highscoreRecord->find('text', 0)),
                                    "rankValue" => $rankValue
                                )
                            )
                        );
                        // array_push($playerNames, $playerName);
                        array_push($highscorePlayersArr, $player);
                    }
                }
            }
        }

        // $resultArr = array_combine($playerNames, $highscorePlayersArr);
        file_put_contents("{$root}/api/adminApi/data/{$worldName}/highscores.json", json_encode($highscorePlayersArr));
    }

    // Public interface
    public function transformAllFiles(string $worldName) {
        $this->transformOnlinePlayers($worldName);
        $this->transformHighscores($worldName);
        return $this;
    }
}