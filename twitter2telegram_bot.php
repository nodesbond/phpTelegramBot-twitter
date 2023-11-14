<?php
require_once 'vendor/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Request;

// Twitter API credentials
$consumerKey = '...';
$consumerSecret = '...';
$accessToken = '...';
$accessTokenSecret = '...';

// Telegram Bot Token
$telegramToken = '...';

// Twitter OAuth
$twitter = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);

// Telegram Bot
$telegram = new Telegram($telegramToken);

// Command handler
function handleCommand($messageText, $chatId) {
    global $twitter;

    $tweets = $twitter->get("search/tweets", ["q" => $messageText, "count" => 5, "tweet_mode" => "extended"]);
    foreach ($tweets->statuses as $tweet) {
        $responseText = "Tweet from @" . $tweet->user->screen_name . ":\n" . $tweet->full_text;
        $response = [
            'chat_id' => $chatId,
            'text' => $responseText
        ];
        Request::sendMessage($response);
    }
}

// Process updates from Telegram
try {
    $telegram->handle();
    $update = json_decode(file_get_contents('php://input'), true);
    $chatId = $update['message']['chat']['id'];
    $messageText = $update['message']['text'];
    handleCommand($messageText, $chatId);
} catch (Exception $e) {
    // Log exceptions
    error_log($e->getMessage());
}
?>
