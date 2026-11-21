<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShortUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $url = trim((string) $this->input('url'));

        // "exemplo.com" vira "https://exemplo.com" (o mesmo é feito no front).
        if ($url !== '' && preg_match('#^[a-z][a-z0-9+.-]*://#i', $url) !== 1) {
            $url = 'https://'.$url;
        }

        $code = trim((string) $this->input('code'));

        $this->merge([
            'url' => $url,
            'code' => $code === '' ? null : strtolower($code),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'url' => [
                'required',
                'string',
                'max:'.config('short.max_url_length'),
                'url:http,https',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $host = parse_url((string) $value, PHP_URL_HOST);

                    // Exige um domínio com TLD (ou localhost/IP) — evita "https://abc".
                    if (! is_string($host) || ($host !== 'localhost' && ! str_contains($host, '.'))) {
                        $fail('Informe uma URL válida.');
                    }
                },
            ],
            'code' => [
                'nullable',
                'string',
                'min:'.config('short.custom_code_min'),
                'max:'.config('short.custom_code_max'),
                'regex:'.config('short.custom_code_pattern'),
                Rule::notIn((array) config('short.reserved_codes')),
                Rule::unique('urls', 'short_url'),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'url.required' => 'Informe a URL que deseja encurtar.',
            'url.url' => 'Informe uma URL válida.',
            'url.max' => 'A URL pode ter no máximo :max caracteres.',
            'code.min' => 'O código precisa ter pelo menos :min caracteres.',
            'code.max' => 'O código pode ter no máximo :max caracteres.',
            'code.regex' => 'Use apenas letras minúsculas, números e hífens (sem hífen no início ou fim).',
            'code.not_in' => 'Este código é reservado pelo sistema. Escolha outro.',
            'code.unique' => 'Este código já está em uso. Escolha outro.',
        ];
    }

    public function originalLink(): string
    {
        return (string) $this->validated('url');
    }

    public function customCode(): ?string
    {
        $code = $this->validated('code');

        return is_string($code) ? $code : null;
    }
}
