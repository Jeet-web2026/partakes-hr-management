<?php

namespace App\Filament\Candidatemanagement\Pages;

use BackedEnum;
use Filament\Pages\Page;

class Kye extends Page
{
    protected static ?string $title = 'KYE';
    protected static ?string $navigationLabel = 'KYE';
    protected static ?string $slug = 'kye';
    protected static ?int $navigationSort = 3;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-magnifying-glass';

    protected string $view = 'filament.pages.kye';

    public function mount(): void
    {
        $this->redirect(route('contact-app.redirection', ['pageName' => 'contacts']), navigate: false);
    }
}
