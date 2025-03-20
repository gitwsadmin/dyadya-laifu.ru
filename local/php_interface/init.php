<?php
ini_set('memory_limit', '512M');
const LOCAL_DEVELOPMENT = 0;



/**
 * Logs a message to Telegram.
 *
 * @param mixed $messageText The message to be logged.
 * @param string|null $groupChatId Optional group chat ID.
 * @return void
 */
function LogTG($messageText, $groupChatId = null)
{
    $botToken = "5865641167:AAF55jrqMP0zFAGrU7Bv-1KUDj_7chXsVWc";
    $defaultChatId = "141079661";
    $chatId = $groupChatId ? $groupChatId : $defaultChatId;

    $telegramUrl = "https://api.telegram.org/bot".$botToken."/sendMessage";
    $text = $_SERVER["HTTP_HOST"]. "\n";
    $text .= print_r($messageText, true);
    $telegram_params = [
        'chat_id' => $chatId,
        'text' => $text,
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($telegram_params),
        ],
    ];

    $context  = stream_context_create($options);
    file_get_contents($telegramUrl, false, $context);
}

/**
 * Удобная принтилка
 * @param $ar
 * @param $dark
 * @param $die
 * @return string|void
 */
function pr($ar, $dark = false, $die = false)
{
    global $USER;
    if (!$USER->IsAdmin()) return "";

    if(!$dark)
    {
        echo "<pre style='font-size:11px;line-height:1.2; padding:5px;'>".print_r($ar, 1)."</pre>";
    }
    else
    {
        echo '<pre style="line-height:1.2; padding:2em;font-size:11px;background: #282c34; color: #61dafb">' .print_r($ar, true).'</pre>';
    }
    if($die) die();
}

require_once "functions.php";
