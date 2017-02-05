<?php

namespace LArtie\MagtuTelegramBot\Commands;

use LArtie\MagtuTelegramBot\Core\User;
use Telegram\Bot\Commands\Command;

class SetCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "set";

    /**
     * @var string Command Description
     */
    protected $description = "Указать свою группу";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        if (empty($arguments)) {
            $this->replyWithMessage([
                'text' => "Команда /set должна содержать идентификатор группы." . PHP_EOL . "Пример: /set 1" . PHP_EOL . PHP_EOL . "Получить идентификатор группы вы можете использовав команду /search",
            ]);
        } else if (($arguments = intval($arguments)) && ($arguments > 0)) {

            /** @var User $user */
            $user = User::find($this->getUpdate()->getMessage()->getChat()->getId());

            $response = '';

            if ($user) {
                $user->group_id = $arguments;
                $user->save();

                $response = "Успешно добавлен идентификатор группы: {$user->group_id}" . PHP_EOL .
                        'Вы можете получить расписание для вашей группы написав боту: ' . PHP_EOL .
                        'Расписание на сегодня' . PHP_EOL .
                        'Расписание на завтра' . PHP_EOL .
                        'Расписание на вторник' . PHP_EOL .
                        'Расписание на среду нечетной недели' . PHP_EOL .
                        'Расписание на понедельник четной недели';
            } else {
                $response = 'Извините, что-то пошло не так. Вашего телеграм аккаунта нету в нашей БД. Пожалуйста выполните команду /start для повторной регистрации.';
            }

            $this->replyWithMessage([
                'text' => $response,
            ]);


        } else {
            $this->replyWithMessage([
                'text' => 'Команда /set может принимать только цифровое значение больше чем 0',
            ]);
        }
    }
}