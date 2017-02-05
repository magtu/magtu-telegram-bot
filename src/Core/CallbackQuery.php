<?php

namespace LArtie\MagtuTelegramBot\Core;

class CallbackQuery
{
    private $data;
    private $isCallbackQuery = false;
    private $commands;

    /**
     * CallbackQuery constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;

        if (array_has($this->data, 'callback_query')) {
            $this->isCallbackQuery = true;

            $this->data->callback_query->data = json_decode(trim($this->data->callback_query->data, '"'));
            $this->data = $this->data->callback_query;
        }

        $this->commands = [
            new PaginateCommand(),
        ];
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return boolean
     */
    public function isCallbackQuery()
    {
        return $this->isCallbackQuery;
    }

    /**
     *
     */
    public function commandsHandler()
    {
        foreach ($this->commands as $command) {
            if ($command->getName() == $this->data->data->command) {

                $command->handle($this->data->from->id, $this->data->data);
            }
        }
    }
}