<?php
//test
$extractManager = new ExtractManager();
//$extractManager->

$worldList = array("Pyra", "Premia", "Pacera", "Secura", "Relania", "Talera");

$extractWorldList = new ExtractWorldList();
$extractManager->setStrategy($extractWorldList);
$extractManager->getStrategy()->extract();

$onlineList = new ExtractOnlinePlayerList("Calmera");
$highscoreList = new ExtractHighscoreList("Calmera");
foreach($worldList as $world) {
    $onlineList->setWorldName($world);
    $highscoreList->setWorldName($world);

    $extractManager->setStrategy($onlineList);
    $extractManager->getStrategy()->extract();
    $extractManager->setStrategy($highscoreList);
    $extractManager->getStrategy()->extract();
}