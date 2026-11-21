<?php

namespace App\Services;

class ShortCodeGenerator
{
    /**
     * @param  list<string>  $reserved
     */
    public function __construct(
        private readonly string $alphabet,
        private readonly int $length,
        private readonly array $reserved,
    ) {}

    public function generate(): string
    {
        do {
            $code = RandomString::from($this->alphabet, $this->length);
        } while ($this->isReserved($code));

        return $code;
    }

    public function isReserved(string $code): bool
    {
        return in_array(strtolower($code), array_map('strtolower', $this->reserved), true);
    }
}
