<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnlockShortUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $code = trim((string) $this->input('code'));

        // Aceita tanto o código quanto a URL curta completa colada.
        if (str_contains($code, '/')) {
            $code = basename((string) parse_url($code, PHP_URL_PATH));
        }

        $this->merge(['code' => $code, 'pin' => trim((string) $this->input('pin'))]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:'.config('short.custom_code_max')],
            'pin' => ['required', 'string', 'max:20'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Informe o código ou a URL curta.',
            'pin.required' => 'Informe o PIN de gerenciamento.',
        ];
    }
}
