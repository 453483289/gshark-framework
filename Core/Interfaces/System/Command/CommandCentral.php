<?php

namespace System\Command;

use System\Configuration;
use System\Command\CommandMaster;
use Classes\GlobalAction;

class CommandCentral
{
    protected $command;
    protected $chat_id;
    protected $bot;
    protected $configuration;
    protected $commandArgv = [];
    protected $commandArgc = 0;
    protected $command_list_master;

    public function __construct($command, $chat_id)
    {
        $this->configuration = Configuration::getConfigElement();
        $this->bot = $this->configuration->telegram->bot_id;
        $this->command_list_master = (array) $this->configuration->commands->master;
        $this->command = $command;
        $this->chat_id = $chat_id;
    }

    public function getCommand()
    {
        if(isset($this->command, $this->chat_id))
        {
            $this->parseArgv();
            $command = $this->command;
            if(isset($this->command_list_master[$command]))
            {
                $master = new CommandMaster($this->command,$this->commandArgv,$this->commandArgc,$this->chat_id);
                $function = $this->command_list_master[$command]->function;
                $master->executeCmd($function);
                return true;
            }
        }
        GlobalAction::sendMessage('*error:* command not found \help for more information', $this->chat_id);
    }

    protected function parseArgv()
    {
        if(stristr($this->command,' '))
        {
            $information = explode(' ', $this->command, 2);
            $this->commandArgv = explode(' ', $information[1]);
            $this->command = $information[0];
            $this->commandArgc = count($this->commandArgv);
        }
    }

}