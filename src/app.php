<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

$blogConfigFile = __DIR__ .'/../config/blog_config.php';
$arrBlogConfig = include $blogConfigFile;
$blogConfig = $arrBlogConfig["blogConfig"];

$cli = new Goutte\Client();

foreach ($blogConfig as $db => $item) {
    $crawler = $cli->request('GET', $item['url']);

    $urls = $crawler->filter('table.kiji_head');

    $fileName = __DIR__ . '/../data/' . $db . '.db';
    $newData = $urls->text();

    $oldData = file_get_contents($fileName);

    if ($newData !== $oldData) {
        $text = $item['text'];
        $text .= $newData . PHP_EOL;
        $text .= $item['url'];
        $text = urlencode($text);
        $url = "https://slack.com/api/chat.postMessage?token=" . SLACK_API_KEY . "&channel=%23" . $db . "_blog&text=" . $text;
        file_get_contents($url);
    }

    file_put_contents($fileName, $newData);
}