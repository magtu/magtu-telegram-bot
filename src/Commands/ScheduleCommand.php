<?php

namespace LArtie\MagtuTelegramBot\Commands;

use Carbon\Carbon;
use LArtie\MagtuAPI\Core\API as Magtu;
use LArtie\MagtuTelegramBot\Core\User;
use Telegram\Bot\Laravel\Facades\Telegram;

/**
 * Требуется улучшение структуры кода @todo
 *
 * Class ScheduleCommand
 * @package LArtie\MagtuTelegramBot\Commands
 */
class ScheduleCommand
{
    private $data;

    /**
     * ScheduleCommand constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data->message;
    }

    /**
     * @return mixed
     */
    private function getUserId()
    {
        return $this->data->from->id;
    }

    /**
     *
     */
    public function handle()
    {
        $userId = $this->getUserId();

        $user = User::find($userId);

        if ($user) {

            if (is_null($user->group_id)) {

                Telegram::sendMessage([
                    'chat_id' => $userId,
                    'text' => 'Вы не указали свою группу по умолчанию.' . PHP_EOL . PHP_EOL . 'Воспользуйтесь командой /set',
                ]);

            } else {

                $day = $this->getDay();

                if ($day == 0) {
                    Telegram::sendMessage([
                        'chat_id' => $userId,
                        'text' => "Ошибка! День недели указан некорректно!",
                    ]);
                } else {
                    $week = $this->getWeek();

                    if (is_null($week)) {
                        Telegram::sendMessage([
                            'chat_id' => $userId,
                            'text' => "Ошибка! Неделя указана некорректно!",
                        ]);
                    } else {

                        $magtu = new Magtu();
                        $schedule = $magtu->getSchedule($user->group_id);

                        Telegram::sendMessage([
                            'chat_id' => $userId,
                            'text' => $this->parseSchedule($schedule, $day, $week),
                            'parse_mode' => 'Markdown',
                        ]);
                    }
                }
            }
        } else {
            Telegram::sendMessage([
                'chat_id' => $userId,
                'text' => 'Извините, что-то пошло не так. Вашего телеграм аккаунта нету в нашей БД. Пожалуйста выполните команду /start для повторной регистрации.',
            ]);
        }
    }

    /**
     *
     * @param $schedule
     * @param $selectedDay
     * @param $week
     * @return string
     */
    private function parseSchedule($schedule, $selectedDay, $week)
    {
        $schedule = $schedule[$week];

        $text = '';
        $dayName = '';

        foreach ($schedule->days as $day) {

            $dayName = $day->day;

            if ($day->day_id == 7) {
                $text .= "Занятий нет.";
                break;
            }

            if ($day->day_id == $selectedDay) {

                $day->events = $this->sortSchedule($day->events);

                foreach ($day->events as $event) {
                    $subgroup = $event->subgroup > 0 ? "Подгруппа: {$event->subgroup}" : "";

                    $firstLine = "Пара: {$event->event_index}. {$subgroup}" . PHP_EOL;
                    $secondLine = "{$event->course}. " . $this->getType($event->type) . PHP_EOL;
                    $thirdLine = "{$event->location} {$event->teacher}" . PHP_EOL;

                    $text = $firstLine . $secondLine . $thirdLine . PHP_EOL . $text;
                }
                break;
            }
        }

        return "*{$dayName}. {$schedule->week} неделя*" . PHP_EOL . PHP_EOL . $text;
    }

    /**
     * @param $schedule
     * @return
     */
    private function sortSchedule($schedule)
    {
        usort($schedule, function ($a, $b) {

            if ($a->event_index > $b->event_index) {
                return -1;
            } else if ($a->event_index < $b->event_index) {
                return 1;
            } else {
                if ($a->subgroup > $b->subgroup) {
                    return -1;
                } else if ($a->subgroup < $b->subgroup) {
                    return 1;
                } else {
                    return 0;
                }
            }
        });

        return $schedule;
    }

    /**
     * @param $type
     * @return string
     */
    private function getType($type)
    {
        switch ($type) {
            case 'Лабораторные':
                $type = 'лаба';
                break;
            default:
                break;
        }

        return $type;
    }

    /**
     * @return int
     */
    private function getDay()
    {
        $args = explode(' ', $this->data->text);

        $day = 0;

        if (isset($args[2])) {

            switch (strtolower($args[2])) {
                case 'понедельник':
                case 'пн':
                    $day = 1;
                    break;
                case 'вторник':
                case 'вт':
                    $day = 2;
                    break;
                case 'среду':
                case 'среда':
                case 'ср':
                    $day = 3;
                    break;
                case 'четверг':
                case 'чт':
                    $day = 4;
                    break;
                case 'пятницу':
                case 'пятница':
                case 'пт':
                    $day = 5;
                    break;
                case 'субботу':
                case 'суббота':
                case 'сб':
                    $day = 6;
                    break;
                case 'воскресенье':
                case 'вс':
                    $day = 7;
                    break;
                case 'сегодня':
                    $now = Carbon::now('Asia/Yekaterinburg');
                    $day = $now->dayOfWeek;
                    break;
                case 'завтра':
                    $now = Carbon::now('Asia/Yekaterinburg');
                    $day = $now->dayOfWeek + 1;
                    break;
            }
        }

        return $day;
    }

    /**
     * @return int
     */
    private function getWeek()
    {
        $args = explode(' ', $this->data->text);

        $week = NULL;

        if (isset($args[3])) {

            switch (strtolower($args[3])) {
                case 'нечетную':
                case 'нечётную':
                case 'нечетная':
                case 'нечётная':
                case 'нечетной':
                case 'нечётной':
                    $week = 0;
                    break;
                case 'четная':
                case 'чётная':
                case 'четную':
                case 'чётную':
                case 'четной':
                case 'чётной':
                    $week = 1;
                    break;
            }
        } else {
            $now = Carbon::now('Asia/Yekaterinburg');
            $week = $now->weekOfYear % 2 === 0 ? 1 : 0;
        }

        return $week;
    }
}