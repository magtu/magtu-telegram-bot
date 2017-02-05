<?php

namespace LArtie\MagtuTelegramBot\Core;

abstract class CallbackQueryCommands
{
    /**
     * The name of the Telegram command.
     * Ex: help - Whenever the user sends /help, this would be resolved.
     *
     * @var string
     */
    protected $name;

    /**
     * {@inheritdoc}
     */
    abstract public function handle($chatId, $arguments);
}