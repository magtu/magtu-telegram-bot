<?php

namespace LArtie\MagtuTelegramBot\Commands;

use LArtie\MagtuAPI\Core\API as MagtuAPI;
use LArtie\MagtuTelegramBot\Core\Paginate;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class SearchCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "search";

    private $max = 9;

    /**
     * @var string Command Description
     */
    protected $description = "Найти свою группу по аббревиатуре";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {

        switch ($arguments) {
            case "":

                $this->replyWithMessage([
                    'text' => "Команда /search должна содержать минимум одну букву названия группы." . PHP_EOL .
                        "Пример: /search а",
                ]);

                break;
            default:

                $groups = (new MagtuAPI())->getGroups($arguments);
                $count = count($groups);

                if ($count > 0) {

                    $total = ceil($count / $this->max);
                    $text = $this->prepareGroups($groups, 0);

                    $paginate = new Paginate();
                    $keyboard = $paginate->make(1, $total, $arguments);

                    $this->replyWithMessage(array_merge([
                        'text' => $text,
                    ], $keyboard));

                } else {
                    $this->replyWithMessage([
                        'text' => 'Совпадений не найдено.',
                    ]);
                }
                break;
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