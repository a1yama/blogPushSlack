<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

$blogConfigFile = __DIR__ .'/../config/blog_config.php';
$arrBlogConfig = include $blogConfigFile;
$blogConfig = $arrBlogConfig["blogConfig"];

$cli = new Goutte\Client();

foreach ($blogConfig as $db => $item) {
    $crawler = $cli->request('GET', $item['url']);

    $urls = $crawler->filter('p.ttl a')->extract(array('_text', 'href'));

    $fileName = __DIR__ . '/../data/' . $db . '.db';
    // 一番上のブログを取得
    // [0]->タイトル
    // [1]->url
    $title = $urls[0][0];
    $blog_url = $urls[0][1];
    $newData = $title;

    $oldData = file_get_contents($fileName);

    if ($newData !== $oldData) {
        $text = $item['text'] . PHP_EOL . PHP_EOL;
        $text .= $title . PHP_EOL . PHP_EOL;
        $text .= 'http://www.keyakizaka46.com' . $blog_url;
        $text = urlencode($text);
        $url = "https://slack.com/api/chat.postMessage?token=" . SLACK_API_KEY . "&channel=%23" . $db . "_blog&text=" . $text;
        file_get_contents($url);
    }

    file_put_contents($fileName, $newData);
}