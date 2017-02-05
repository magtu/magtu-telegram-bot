<?php

/**
 * Telegram API Bot routes
 */
Route::group(['prefix' => 'telegram', 'as' => 'telegram'], function() {

    /**
     * Определение роутов для бота https://telegram.org/magtu_bot
     */
    Route::group(['prefix' => 'magtu_bot', 'as' => '.magtu_bot'], function () {

        /**
         * Вебхук для текущего бота
         */
        Route::any('webhook/' . env('TELEGRAM_BOT_TOKEN'), ['as' => '.webhook', 'uses' => 'LArtie\MagtuTelegramBot\Controllers\TelegramBotController@webhook']);
    });
});