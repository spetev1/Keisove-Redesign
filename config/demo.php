<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Demo Password
    |--------------------------------------------------------------------------
    |
    | While the site is an unreleased client demo it is shared over a public
    | tunnel URL. Setting a password here puts a gate in front of every page.
    | Leave it empty to disable the gate entirely - appropriate once the store
    | goes live, not before.
    |
    */

    'password' => env('DEMO_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Sale Deadline
    |--------------------------------------------------------------------------
    |
    | The moment the homepage's countdown band runs to. Left empty the band
    | counts down to the end of the current week, which keeps a demo link that
    | is opened over several weeks from showing an expired promotion. Set it to
    | a date and time to pin the countdown for a scheduled pitch.
    |
    */

    'sale_ends_at' => env('DEMO_SALE_ENDS_AT'),

];
