<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ValidDocumentUpload implements ValidationRule
{
    protected array $allowedMimes = [
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'jpg',
        'jpeg',
        'png',
    ];

    protected int $maxSizeKb = 1024;

    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            $fail('The :attribute must be a valid uploaded file.');
            return;
        }

        if (! $value->isValid()) {
            $fail('The :attribute failed to upload correctly or is corrupt.');
            return;
        }
        $extension = strtolower($value->getClientOriginalExtension());

        if (! in_array($extension, $this->allowedMimes)) {
            $fail('The :attribute must be a file of type: PDF, Word, Excel, JPG, JPEG, or PNG.');
            return;
        }

        if ($value->getSize() / 1024 > $this->maxSizeKb) {
            $fail('The :attribute must not be greater than 1 MB.');
            return;
        }

        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            if (@getimagesize($value->getRealPath()) === false) {
                $fail('The :attribute appears to be a corrupt image file.');
                return;
            }
        }

        if ($extension === 'pdf') {
            $handle = fopen($value->getRealPath(), 'r');
            $header = fread($handle, 5);
            fclose($handle);

            if ($header !== '%PDF-') {
                $fail('The :attribute appears to be a corrupt PDF file.');
                return;
            }
        }
    }
}
