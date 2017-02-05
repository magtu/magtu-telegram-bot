<?php

namespace LArtie\MagtuTelegramBot\Controllers;

use LArtie\MagtuTelegramBot\Commands\ScheduleCommand;
use LArtie\MagtuTelegramBot\Core\CallbackQuery;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Requests;
use Telegram\Bot\Laravel\Facades\Telegram;
use LArtie\MagtuTelegramBot\Commands\StartCommand;
use LArtie\MagtuTelegramBot\Commands\SetCommand;
use LArtie\MagtuTelegramBot\Commands\SearchCommand;
use LArtie\MagtuTelegramBot\Commands\HelpCommand;

/**
 * Class TelegramBotController
 * @package LArtie\MagtuTelegramBot\Controllers
 */
final class TelegramBotController extends Controller
{
    /**
     * С данным методом установлен веб хук для бота @magtu_bot
     * Принимает в php://input данные о новом сообщении и обрабатывает их
     * через командную шину SDK Telegram
     * @param Request $request
     */
    public function webhook(Request $request)
    {
        Telegram::addCommands([
            StartCommand::class,
            SetCommand::class,
            SearchCommand::class,
            HelpCommand::class,
        ]);

        $telegramContent = json_decode($request->getContent());

        if (isset($telegramContent->message->text)) {

            /**
             * Сделано конечно по уебански и вообще не гибко и нихуя не don't repeat your self
             * но бля. мне лень делать нормально. если в либу внесут такие возможности, тогда без б. я исправлю
             */
            if (starts_with(mb_strtolower($telegramContent->message->text), "расписание на")) {

                $schedule = new ScheduleCommand($telegramContent);
                $schedule->handle();

            } else {
                Telegram::commandsHandler(true);
            }
        } else {

            /**
             * В библиотеке php sdk telegram слишком долго выходят обновы, но они все таки выйдут
             * поэтому решением было побыстренькому отписать маленький класс, который будет
             * обрабаывать нужные колбэки. Но в будущем осуществить рефакторинг дабы сохранить целостность
             * проекта и простоту в исполнении
             */
            $cq = new CallbackQuery($telegramContent);

            if ($cq->isCallbackQuery()) {
                $cq->commandsHandler();
            }
        }
    }

    /**
     * Отправляет сообщение https://telegram.me/lartie
     * @param Request $request
     */
    public function sendMessage(Request $request)
    {
        $this->validate($request, [
            'text' => 'required|min:16'
        ]);

        Telegram::sendMessage([
            'chat_id' => 36983349,
            'text' => $request->input('text'),
        ]);
    }
}
