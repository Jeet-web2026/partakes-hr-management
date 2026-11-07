<?php

namespace App\Enums;

enum DesignationType: string
{
    case Recruitment = 'recruitment';
    case Employment  = 'employment';

    public function label(): string
    {
        return match ($this) {
            self::Recruitment => 'Recruitment',
            self::Employment  => 'Employment',
        };
    }
}