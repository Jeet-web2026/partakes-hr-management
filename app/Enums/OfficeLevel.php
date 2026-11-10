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
            self::Global => 'Global Office <  Primary',
            self::Continental => 'Continental Office < Global Office',
            self::CountryHead => 'Country Head Office < Continental Office',
            self::Registered => 'Registered Office < Country Head Office',
            self::LocalHead => 'Local Head Office < Registered Office',
            self::ZonalHead => 'Zonal Head Office < Local Head Office',
            self::Branch => 'Branch Office < Zonal Head Office',
        };
    }
}
