<?php

namespace App\Services\Common;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileStorageService
{
    public function store(
        UploadedFile $file,
        string $folder,
        int|string $primaryKey,
        int|string $orgUin,
        int|string $userUin,
        string $disk = 'public',
        ?string $storageType = null
    ): string {
        $fileName = $this->buildFileName($storageType, $file, $primaryKey, $orgUin, $userUin);

        return $file->storeAs($this->buildFolderPath($folder), $fileName, $disk);
    }

    public function replace(
        ?string $oldPath,
        UploadedFile $file,
        string $folder,
        int|string $primaryKey,
        int|string $orgUin,
        int|string $userUin,
        string $disk = 'public'
    ): string {
        if ($oldPath) {
            $this->delete($oldPath, $disk);
        }

        return $this->store($file, $folder, $primaryKey, $orgUin, $userUin, $disk);
    }

    public function delete(?string $path, string $disk = 'public'): bool
    {
        if (empty($path)) {
            return false;
        }

        return Storage::disk($disk)->exists($path)
            ? Storage::disk($disk)->delete($path)
            : false;
    }

    protected function buildFolderPath(string $folder): string
    {
        return trim($folder, '/');
    }

    protected function buildFileName(
        ?string $storageType,
        UploadedFile $file,
        int|string $primaryKey,
        int|string $orgUin,
        int|string $userUin
    ): string {
        $extension = $file->getClientOriginalExtension();

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeOriginalName = Str::slug($originalName);

        return sprintf(
            '%s_%s_%s_%s&+&%s.%s',
            $storageType ?? 'file',
            $primaryKey,
            $orgUin,
            $userUin,
            $safeOriginalName,
            $extension
        );
    }

    public function downloadAs(string $path, string $disk = 'public'): StreamedResponse
    {
        if (!Storage::disk($disk)->exists($path)) {
            abort(404, 'File not found.');
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $downloadName = 'download_' . now()->format('Ymd_His') . ($extension ? ".{$extension}" : '');

        return Storage::disk($disk)->download($path, $downloadName);
    }
}
