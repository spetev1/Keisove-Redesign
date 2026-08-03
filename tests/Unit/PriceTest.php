<?php

namespace Tests\Unit;

use App\Support\Price;
use PHPUnit\Framework\TestCase;

class PriceTest extends TestCase
{
    public function test_it_formats_stotinki_as_leva(): void
    {
        $this->assertSame('22.90 лв.', Price::format(2290));
    }

    public function test_it_groups_thousands_with_a_space(): void
    {
        $this->assertSame('1 234.56 лв.', Price::format(123456));
    }

    public function test_it_converts_to_euro_at_the_fixed_rate(): void
    {
        // 22.45 лв. at 1.95583 is 11.4785..., which rounds to 11.48.
        $this->assertSame('11.48 €', Price::formatEur(2245));
    }

    /**
     * Rounded once at the end rather than per lev, so the euro figure cannot
     * drift from what dividing the whole amount gives.
     */
    public function test_it_rounds_the_euro_figure_only_once(): void
    {
        $this->assertSame('35.23 €', Price::formatEur(6890));
    }

    public function test_it_converts_zero(): void
    {
        $this->assertSame('0.00 €', Price::formatEur(0));
    }
}
