<?php

namespace LArtie\MagtuTelegramBot\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class HelpCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "help";

    /**
     * @var string Command Description
     */
    protected $description = "Получить список команд";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        $commands = $this->telegram->getCommands();

        //if (empty($arguments)) {

            $text = '';
            foreach ($commands as $name => $handler) {
                $text .= sprintf('/%s - %s' . PHP_EOL, $name, $handler->getDescription());
            }
        //}

        $text .= PHP_EOL . 'Вы можете получить расписание для вашей группы написав боту: ' . PHP_EOL .
            'Расписание на сегодня' . PHP_EOL .
            'Расписание на завтра' . PHP_EOL .
            'Расписание на вторник' . PHP_EOL .
            'Расписание на понедельник четной недели' . PHP_EOL .
            'Расписание на среду нечетной недели';

        $this->replyWithMessage(compact('text'));
    }
}