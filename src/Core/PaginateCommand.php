<?php

namespace LArtie\MagtuTelegramBot\Core;

use Telegram\Bot\Laravel\Facades\Telegram;
use LArtie\MagtuAPI\Core\API as MagtuAPI;

/**
 * ОСТОРОЖНО!!! ВНИЗУ ЧУТЬ МЕНЬШЕ ГОВНОКОДА ЧЕМ БЫЛО РАНЬШЕ, НО ВСЕРАВНО НЕ СТОИТ РИСКОВАТЬ @todo переписать с выпуском tg sdk php 3.0
 * Class PaginateCommand
 * @package App
 */
class PaginateCommand extends CallbackQueryCommands
{
    protected $name = 'paginate';

    private $max = 9;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $chatId
     * @param $arguments
     */
    public function handle($chatId, $arguments)
    {
        $current = $arguments->data->to;
        $filter = $arguments->data->filter;

        $groups = (new MagtuAPI())->getGroups($filter);
        $count = count($groups);

        if ($count > 0) {
            $total = ceil($count / $this->max);

            $start = $this->max * ($current - 1);

            $text = $this->prepareGroups($groups, $start);

            $paginate = new Paginate();
            $keyboard = $paginate->make($current, $total, $filter);

            Telegram::sendMessage(array_merge([
                'chat_id' => $chatId,
                'text' => $text,
            ], $keyboard));

        } else {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => 'Совпадений не найдено.',
            ]);
        }
    }

    /**
     * @param $groups
     * @param $start
     * @return string
     */
    public function prepareGroups($groups, $start)
    {
        $groups = array_slice($groups, $start, $this->max);
        return $this->parseGroups($groups);
    }

    /**
     * @param $groups
     * @return string
     */
    private function parseGroups($groups)
    {
        $text = "Чтобы выбрать группу выполните команду /set с идентификатором группы".PHP_EOL.PHP_EOL;

        foreach ($groups as $group) {
            $text .= "{$group->id}. {$group->name}\n";
        }

        return $text . PHP_EOL . "Пример: /set 1";
    }
}