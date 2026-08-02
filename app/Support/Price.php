<?php

namespace App\Support;

class Price
{
    /**
     * Money is held as an integer of stotinki everywhere in the app; this is
     * the single place it becomes a string a shopper reads.
     */
    public static function format(int $stotinki): string
    {
        return number_format($stotinki / 100, 2, '.', ' ').' лв.';
    }
}
