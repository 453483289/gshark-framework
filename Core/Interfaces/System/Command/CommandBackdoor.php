<?php

namespace System\Command;

use Database\Database;
use System\Configuration;
use \PDO;

class CommandBackdoor extends Command
{
    public function __construct($command, $commandArgv, $commandArgc, $chat_id)
    {
        $this->configuration = Configuration::getConfigElement();
        $this->bot = $this->configuration->telegram->bot_id;
        $this->chat_id = $chat_id;
        $this->command = $command;
        $this->commandArgc = $commandArgc;
        $this->commandArgv = $commandArgv;
        $this->bdd = Database::get_database();
    }

    public function BackdoorResume()
    {
        $this->returnMessage('Backdoor resume');
    }
}