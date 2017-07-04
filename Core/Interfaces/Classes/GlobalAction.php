<?php

namespace Classes;
use System\Configuration;
use Database\Database;

class GlobalAction
{
    public static function sendMessage($message, $chat_id)
    {
        $configuration = Configuration::getConfigElement();
        $bot = $configuration->telegram->bot_id;
        if($message !== null)
            GlobalAction::sendPost('https://api.telegram.org/bot'.$bot.'/sendmessage',['text' => $message, 'chat_id' => $chat_id,'parse_mode' => 'Markdown']);
        return true;
    }

    public static function moduleMessage($message, $chat_id)
    {
        $message = "*Module output: *".$message;
        $configuration = Configuration::getConfigElement();
        $bot = $configuration->telegram->bot_id;
        if($message !== null)
            GlobalAction::sendPost('https://api.telegram.org/bot'.$bot.'/sendmessage',['text' => $message, 'chat_id' => $chat_id,'parse_mode' => 'Markdown']);
        return true;
    }

    public static function sendPost($url, $data)
    {
        $proxy = Database::getProxy();
        if($proxy)
            $check = Configuration::checkProxy($proxy->proxy_host, $proxy->proxy_port);
        else
            $check = true;
        if(!$check)
        {
            $proxy = false;
        }
        if($proxy === false) {
            $options = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($data)
                )
            );
        }
        else
        {
            $options = array(
                'http' => array(
                    'proxy' => 'socks://'.$proxy->proxy_host.':'.$proxy->proxy_port,
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($data)
                )
            );
        }
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) {
            return false;
        }
        return $result;
    }

    public static function pingURL($url)
    {
        $headers = @get_headers($url);
        if(strpos($headers[0],'200')===false)
            return false;
        return true;

    }
}