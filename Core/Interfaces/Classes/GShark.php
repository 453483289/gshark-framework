<?php

namespace Classes;
use Database\Database;
use System\Configuration;
use System\Command\CommandCentral;

class GShark
{
    
    protected $bot;
    protected $command;
    protected $chatId;
    protected $configuration;
    private $bdd;

    protected $route = [
      'webook' => 'loadWebook',
    ];



    public function __construct(){
        $this->configuration = Configuration::getConfigElement();
        $this->bot = $this->configuration->telegram->bot_id;
        $this->bdd = Database::get_database();
    }

    public function routing()
    {
        if(isset($_GET['p'], $this->route[$_GET['p']]))
        {
            $route = $this->route[$_GET['p']];
            $this->$route();
        }
    }



    protected function loadWebook()
    {
        $content = @file_get_contents("php://input");
        $update = json_decode($content, TRUE);
        if(isset($update["message"]))
        {
            $message = $update["message"];
            if(isset($message["chat"]["id"]))
                $this->chatId = $message["chat"]["id"];
            if(isset($message['text']))
                $this->command = $message['text'];
            $command_start = new CommandCentral($this->command, $this->chatId);
            $command_start->getCommand();
        }
    }


}