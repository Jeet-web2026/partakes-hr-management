<?php

namespace App\Services\Common;

use App\Models\ShareLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TinyUrlService
{
    public const DEFAULT_TTL_HOURS = 24;

    public function createFor(Model $model, int|string $foreignId, ?int $userUin = null, int $hours = self::DEFAULT_TTL_HOURS): ShareLink
    {
        return ShareLink::create([
            'Link_Foreign_Id_UIN' => $foreignId,
            'Link_Type'           => $model->getMorphClass(),
            'Link_Token'          => $this->generateToken(),
            'Link_Expy_On'        => now()->addHours($hours),
            'CrBy'                => $userUin,
            'MoBy'                => $userUin,
        ]);
    }

    public function resolve(string $token): ?ShareLink
    {
        $link = ShareLink::where('Link_Token', $token)->first();

        if (! $link || $link->isExpired()) {
            return null;
        }

        return $link;
    }

    public function shortUrlFor(ShareLink $link): string
    {
        return route('tiny-url.redirect', ['token' => $link->Link_Token]);
    }

    private function generateToken(): string
    {
        do {
            $token = Str::lower(Str::random(10));
        } while (ShareLink::where('Link_Token', $token)->exists());

        return $token;
    }
}
