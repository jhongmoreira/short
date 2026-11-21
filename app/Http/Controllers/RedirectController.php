<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use App\Services\UserAgentParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function __invoke(Request $request, string $code, UserAgentParser $parser): RedirectResponse
    {
        // Códigos gerados diferenciam maiúsculas; personalizados são minúsculos.
        $url = ShortUrl::query()->where('short_url', $code)->first()
            ?? ShortUrl::query()->where('short_url', strtolower($code))->first();

        abort_if($url === null, 404);

        $url->visualizations()->create([
            'access_date_hour' => now(),
            'device_info' => $parser->parse($request->userAgent()),
        ]);

        return redirect()->away($url->original_link)
            ->header('Cache-Control', 'no-store, max-age=0')
            ->header('X-Robots-Tag', 'noindex');
    }
}
