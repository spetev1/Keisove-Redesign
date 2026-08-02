<?php

namespace App\Support;

/**
 * What a case is made of, or how it is built. Stored as a latin slug so it
 * reads cleanly in a filter URL.
 */
enum CaseType: string
{
    case Silicone = 'silikonov';
    case Hard = 'tvurd';
    case Leather = 'kozhen';
    case Hybrid = 'hibriden';
    case Shockproof = 'udaroustoychiv';

    public function label(): string
    {
        return match ($this) {
            self::Silicone => 'Силиконов',
            self::Hard => 'Твърд',
            self::Leather => 'Кожен',
            self::Hybrid => 'Хибриден',
            self::Shockproof => 'Удароустойчив',
        };
    }
}
