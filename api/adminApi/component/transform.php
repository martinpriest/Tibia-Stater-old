<?php

$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root . '/api/libs/simpleHtmlDOM/simple_html_dom.php');
// baza warehouse do slownikow
require_once($root . '/api/adminApi/component/transformSupportFunctions.php');

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

        // tablice slownikow
        $worldArr = getWorldsArray();
        $residenceArr = getResidencesArray();
        $vocationArr = getVocationArray();

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
                
                // SEX
                if(preg_match('~Sex:</td><td>(.*?)</td>~i', $fileContent, $temp)) {
                    if(strip_tags($temp[1]) == "male") $player['sex']=0;
                    else $player['sex']=1;
                }
                
                // VOCATION
                if(preg_match('~Vocation:</td><td>(.*?)</td>~i', $fileContent, $temp)) {
                    $player['idVocation'] = strip_tags($temp[1]);
                    if(false !== $idVocation = array_search($player["idVocation"], $vocationArr)) $player['idVocation'] = intval($idVocation);
                    else {
                        $player['idVocation'] = createVocation($player['idVocation']);
                        $vocationArr = getVocationArray();
                    }
                }

                if(preg_match('~Level:</td><td>(.*?)</td>~i', $fileContent, $temp)) $player['level'] = intval(strip_tags($temp[1]));
                if(preg_match('~Achievement Points:</nobr></td><td>(.*?)</td>~i', $fileContent, $temp)) $player['achievmentPoints'] = intval(strip_tags($temp[1]));
                //SWIAT
                if(preg_match('~World:</td><td>(.*?)</td>~i', $fileContent, $temp)) {
                    $player['idWorld'] = strip_tags($temp[1]);
                    if(false !== $idWorld = array_search($player["idWorld"], $worldArr)) $player['idWorld'] = intval($idWorld);
                }

                // STARE SWIATY
                if(preg_match('~Former World:</td><td>(.*?)</td>~i', $fileContent, $temp)) {
                    $player['idFormerWorld'] = strip_tags($temp[1]);
                    if(false !== $idFormerWorld = array_search($player["idFormerWorld"], $worldArr)) $player['idFormerWorld'] = intval($idFormerWorld);
                }

                // REZYDENCJA
                if(preg_match('~Residence:</td><td>(.*?)</td>~i', $fileContent, $temp)) {
                    $player['idResidence'] = strip_tags($temp[1]);
                    if(false !== $idResidence = array_search($player["idResidence"], $residenceArr)) $player['idResidence'] = intval($idResidence);
                    else {
                        $player['idResidence'] = createResidence($player['idResidence']);
                        $residenceArr = getResidencesArray();
                    }
                }

                //if(preg_match('~action=characters">(.*?)is paid~i', $fileContent, $temp)) $player['house'] = str_replace('&#160;', ' ', strip_tags($temp[1]));
                //if(preg_match('~Membership:</td><td>(.*?) of the~i', $fileContent, $temp)) $player['guildRank'] = strip_tags($temp[1]);
                //if(preg_match('~action=characters\">(.*?)</a></td>~i', $fileContent, $temp)) $player['guild'] = str_replace('&#160;', ' ', strip_tags(preg_replace("#(.*?)<a (.*?)>#is", '', $temp[1])));
                //if(preg_match('~Last Login:</td><td>(.*?)</td>~i', $fileContent, $temp)) $player['lastLogin'] = str_replace('&#160;', ' ', strip_tags($temp[1]));
                if(preg_match('~Status:</td><td>(.*?)</td>~i', $fileContent, $temp)) {
                    if(strip_tags($temp[1]) == "Free Account") $player['status'] = 0;
                    else $player['status'] = 1;
                }
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

        $characterFilePath = "{$root}/api/adminApi/data/{$worldName}/characters.json";
        $characterFileContent = file_get_contents($characterFilePath);
        $charactersArr = json_decode($characterFileContent, true);


        foreach($highscoreTypes as $type) {
            
            for($i=1; $i<13; $i++) {
                $filePath = "{$root}/api/adminApi/data/{$worldName}/highscores/{$type}/{$i}.html";
                if(is_file($filePath)) {
                    $fileContent = file_get_contents($filePath);
                    $this->filesReadCounter++;
                    $html = str_get_html($fileContent);
                    $tableContent = $html->find('div.InnerTableContainer', 1)->find('tr[style="background-color:#F1E0C6;"], tr[style="background-color:#D4C0A1;"]');
                    
                    // dla kazdego rekordu w tabelkach highscoru
                    foreach($tableContent as $highscoreRecord) {
                        $playerName = utf8_encode($highscoreRecord->find('text', 1));
                        if($type == "experience" || $type == "loyalty") $rankValue = intval(str_replace(',', '', utf8_encode($highscoreRecord->find('text', 4))));
                        else $rankValue = intval(str_replace(',', '', utf8_encode($highscoreRecord->find('text', 3))));
                        
                        $charactersArr[$playerName][$type] = array(
                            "rankPosition" => intval(utf8_encode($highscoreRecord->find('text', 0))),
                            "rankValue" => $rankValue
                        );
                    }
                }
            }
        }

        file_put_contents("{$root}/api/adminApi/data/{$worldName}/afterTransform.json", json_encode($charactersArr));
        // echo date('Y');
        // echo date('m');
        // echo date('d');
        // echo date('N');
    }

    // Public interface
    public function transformAllFiles(string $worldName) {
        $this->transformOnlinePlayers($worldName);
        $this->transformHighscores($worldName);
        return $this;
    }
}