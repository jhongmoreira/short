<?php

namespace App\Services;

/**
 * Gera e confere PINs de gerenciamento.
 *
 * O PIN nunca é salvo em texto puro: armazenamos um HMAC-SHA256 com a
 * APP_KEY, que é determinístico (permite índice único) e não reversível.
 */
class PinManager
{
    public function __construct(
        private readonly string $key,
        private readonly string $alphabet,
        private readonly int $length,
    ) {}

    public function generate(): string
    {
        return RandomString::from($this->alphabet, $this->length);
    }

    public function normalize(string $pin): string
    {
        return strtoupper((string) preg_replace('/[\s-]+/', '', $pin));
    }

    public function hash(string $pin): string
    {
        return hash_hmac('sha256', $this->normalize($pin), $this->key);
    }

    public function matches(string $pin, string $hash): bool
    {
        return hash_equals($hash, $this->hash($pin));
    }
}
