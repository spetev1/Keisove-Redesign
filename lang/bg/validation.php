<?php

/*
 * Only the rules the auth and profile forms actually apply are translated -
 * `required`, `string`, `email`, `min`, `max`, `unique`, `confirmed`,
 * `current_password` and what `Password::default()` can add. Every other rule
 * falls through to the framework's English, which is what
 * `APP_FALLBACK_LOCALE` is for, so this file grows as the forms do rather than
 * carrying two hundred rules the store never validates against.
 *
 * The attributes below are indefinite nouns, because a message has to read as
 * a sentence with any of them dropped into it. Where that could not be made to
 * work in Bulgarian - `confirmed` and the password rules, which would need the
 * noun to agree in gender - the message names the field itself and leaves
 * `:attribute` out. Both are single-purpose here.
 */

return [
    'confirmed' => 'Потвърждението не съвпада.',
    'current_password' => 'Паролата е грешна.',
    'email' => 'Полето :attribute трябва да е валиден имейл адрес.',
    'max' => [
        'string' => 'Полето :attribute не може да е по-дълго от :max символа.',
    ],
    'min' => [
        'string' => 'Полето :attribute трябва да е поне :min символа.',
    ],
    'required' => 'Полето :attribute е задължително.',
    'string' => 'Полето :attribute трябва да е текст.',
    'unique' => 'Вече съществува профил с този :attribute.',

    'password' => [
        'letters' => 'Паролата трябва да съдържа поне една буква.',
        'mixed' => 'Паролата трябва да съдържа поне една главна и една малка буква.',
        'numbers' => 'Паролата трябва да съдържа поне една цифра.',
        'symbols' => 'Паролата трябва да съдържа поне един специален знак.',
        'uncompromised' => 'Тази парола е изтекла при пробив в друг сайт. Избери друга.',
    ],

    'attributes' => [
        'code' => 'код за потвърждение',
        'current_password' => 'текуща парола',
        'email' => 'имейл',
        'name' => 'име',
        'password' => 'парола',
        'password_confirmation' => 'потвърждение на паролата',
        'recovery_code' => 'код за възстановяване',
    ],
];
