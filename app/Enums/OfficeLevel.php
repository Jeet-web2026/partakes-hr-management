<?php

namespace App\Enums;

enum OfficeLevel: int
{
    case Global = 1;
    case Continental = 2;
    case CountryHead = 3;
    case Registered = 4;
    case LocalHead = 5;
    case ZonalHead = 6;
    case Branch = 7;

    public function label(): string
    {
        return match ($this) {
            self::Global => 'Global Office',
            self::Continental => 'Continental Office',
            self::CountryHead => 'Country Head Office',
            self::Registered => 'Registered Office',
            self::LocalHead => 'Local Head Office',
            self::ZonalHead => 'Zonal Head Office',
            self::Branch => 'Branch Office',
        };
    }
}
