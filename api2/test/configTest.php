<?php
$config = AppConfig::getInstance();
$config->setProperty("lipton", "powtarzankoooo");
unset($config);

$config2 = AppConfig::getInstance();
echo $config2->getProperty("lipton");
echo $config2::APII_PATH;