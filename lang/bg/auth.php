<?php

/*
 * What Fortify hands back when signing in fails. Only the keys the store can
 * actually reach are translated - anything left out falls through to the
 * framework's English, which is what `APP_FALLBACK_LOCALE` is for.
 */

return [
    'failed' => 'Тези данни не съвпадат с профил при нас.',
    'password' => 'Паролата е грешна.',
    'throttle' => 'Твърде много опити за вход. Опитай отново след :seconds секунди.',
];
