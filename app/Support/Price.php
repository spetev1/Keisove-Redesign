<?php

namespace App\Support;

class Price
{
    /**
     * The fixed conversion rate Bulgaria adopted the euro at. Printed in the
     * footer as well as used here, so it is named once rather than typed twice.
     */
    public const EUR_RATE = 1.95583;

    /**
     * Money is held as an integer of stotinki everywhere in the app; this is
     * the single place it becomes a string a shopper reads.
     */
    public static function format(int $stotinki): string
    {
        return number_format($stotinki / 100, 2, '.', ' ').' лв.';
    }

    /**
     * The same amount in euro, which the law requires alongside the lev price
     * during changeover. Converted from the stotinki rather than from the
     * formatted string, and rounded once at the end.
     */
    public static function formatEur(int $stotinki): string
    {
        return number_format($stotinki / 100 / self::EUR_RATE, 2, '.', ' ').' €';
    }
}
