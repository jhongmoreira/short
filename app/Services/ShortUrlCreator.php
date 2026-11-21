<?php

namespace App\Services;

use App\Models\ShortUrl;
use Illuminate\Database\UniqueConstraintViolationException;
use RuntimeException;

class ShortUrlCreator
{
    private const MAX_ATTEMPTS = 10;

    public function __construct(
        private readonly ShortCodeGenerator $codes,
        private readonly PinManager $pins,
    ) {}

    /**
     * Cria o link curto. Quando $customCode é informado ele é usado como está
     * (já validado); caso contrário um código aleatório é gerado. Colisões de
     * código gerado ou de PIN são resolvidas com novas tentativas.
     */
    public function create(string $originalLink, ?string $customCode = null): CreatedShortUrl
    {
        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
            $code = $customCode ?? $this->codes->generate();
            $pin = $this->pins->generate();

            try {
                $url = ShortUrl::create([
                    'original_link' => $originalLink,
                    'short_url' => $code,
                    'pin_manager' => $this->pins->hash($pin),
                ]);

                return new CreatedShortUrl($url, $pin);
            } catch (UniqueConstraintViolationException $e) {
                // Código personalizado tomado entre a validação e o insert.
                if ($customCode !== null && ShortUrl::withTrashed()->where('short_url', $customCode)->exists()) {
                    throw $e;
                }
            }
        }

        throw new RuntimeException('Não foi possível gerar um link único.');
    }
}
