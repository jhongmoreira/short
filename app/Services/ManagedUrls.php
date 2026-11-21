<?php

namespace App\Services;

use App\Models\ShortUrl;
use Illuminate\Contracts\Session\Session;

/**
 * Controla, na sessão, quais links o visitante já desbloqueou com o PIN.
 */
class ManagedUrls
{
    private const KEY = 'short.managed';

    public function __construct(private readonly Session $session) {}

    public function grant(ShortUrl $url): void
    {
        $ids = $this->ids();
        $ids[] = $url->url_id;

        $this->session->put(self::KEY, array_values(array_unique($ids)));
    }

    public function allows(ShortUrl $url): bool
    {
        return in_array($url->url_id, $this->ids(), true);
    }

    /**
     * @return list<int>
     */
    private function ids(): array
    {
        $ids = $this->session->get(self::KEY, []);

        return is_array($ids) ? array_values(array_map('intval', $ids)) : [];
    }
}
