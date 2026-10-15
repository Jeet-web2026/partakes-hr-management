<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AdditionalDocuments extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $fileNo = null,
        public ?string $refNo = null,
        public ?string $agreementSigned = null,
        public ?string $signedDocumentReceivedDate = null,
        public ?string $agreementReceivedType = null,
        public ?bool $isDisabled = false,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.additional-documents');
    }
}
