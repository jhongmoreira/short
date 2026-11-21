<?php

namespace App\Services;

use App\Models\ShortUrl;

/**
 * Resultado da criação: o model e o PIN em texto puro (exibido uma única vez).
 */
final readonly class CreatedShortUrl
{
    public function __construct(
        public ShortUrl $url,
        public string $pin,
    ) {}
}
