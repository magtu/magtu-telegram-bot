<?php

namespace LArtie\MagtuTelegramBot\Core;


class Paginate
{
    private $keyboard;

    public function __construct()
    {
        $this->resetKeyBoard();
    }

    /**
     *
     */
    private function resetKeyBoard()
    {
        $this->keyboard = [[]];
    }

    /**
     * @param $current
     * @param $total
     * @param $filter
     * @param bool $resetKeyBoard
     * @return array
     */
    public function make($current, $total, $filter, $resetKeyBoard = true)
    {
        if ($resetKeyBoard) {
            $this->resetKeyBoard();
        }

        if ($total == 1) {
            return [];

        } else if ($current == $total) {
            $this->appendPrev($current, $filter);

        } else if ($current == 1) {
            $this->appendNext($current, $filter);

        } else if ($current > 1 && $current < $total) {
            $this->appendPrev($current, $filter);
            $this->appendNext($current, $filter);
        }

        return $this->getKeyBoard();
    }

    /**
     * @return array
     */
    private function getKeyBoard()
    {
        return [
            'reply_markup' => json_encode([
                'inline_keyboard' => $this->keyboard,
            ]),
        ];
    }

    /**
     * @param $current
     * @param $filter
     * @return mixed
     */
    private function appendNext($current, $filter)
    {
        $to = $current + 1;
        $command = $this->getCallbackCommand($to, $filter);
        $this->appendCommand('Далее', $command);
    }

    /**
     * @param $current
     * @param $filter
     * @return mixed
     */
    private function appendPrev($current, $filter)
    {
        $to = $current - 1;
        $command = $this->getCallbackCommand($to, $filter);
        $this->appendCommand('Назад', $command);
    }

    /**
     * @param $to
     * @param $filter
     * @return mixed
     */
    private function getCallbackCommand($to, $filter)
    {
        return '{"command": "paginate", "data": {"to": "'.$to.'", "filter": "'.$filter.'"}}';
    }

    /**
     * @param $text
     * @param $callback
     */
    private function appendCommand($text, $callback)
    {
        $this->keyboard[0][] = [
            'text' => $text,
            'callback_data' => $callback,
        ];
    }
}