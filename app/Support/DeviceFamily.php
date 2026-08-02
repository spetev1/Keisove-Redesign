<?php

namespace App\Support;

/**
 * The handset family a case or protector is cut for. Stored as a latin slug so
 * it reads cleanly in a filter URL.
 *
 * The order is the order the homepage's brand strip deals them in, four to a
 * set. A family with nothing behind it yet is still named there but is left out
 * of the category filters, which only offer facets that have products.
 */
enum DeviceFamily: string
{
    case IPhone = 'iphone';
    case Samsung = 'samsung';
    case Xiaomi = 'xiaomi';
    case Honor = 'honor';
    case Huawei = 'huawei';
    case Google = 'google';
    case OnePlus = 'oneplus';
    case Motorola = 'motorola';

    /**
     * These are proper nouns, so they stay latin rather than being
     * transliterated into Bulgarian.
     */
    public function label(): string
    {
        return match ($this) {
            self::IPhone => 'iPhone',
            self::Samsung => 'Samsung',
            self::Xiaomi => 'Xiaomi',
            self::Honor => 'Honor',
            self::Huawei => 'Huawei',
            self::Google => 'Google',
            self::OnePlus => 'OnePlus',
            self::Motorola => 'Motorola',
        };
    }
}
