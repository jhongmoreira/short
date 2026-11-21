<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnlockShortUrlRequest;
use App\Models\ShortUrl;
use App\Services\ManagedUrls;
use App\Services\PinManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManageController extends Controller
{
    /**
     * Formulário de acesso: código + PIN.
     */
    public function form(Request $request): View
    {
        return view('manage.unlock', [
            'code' => (string) $request->query('codigo', ''),
        ]);
    }

    public function unlock(UnlockShortUrlRequest $request, PinManager $pins, ManagedUrls $managed): RedirectResponse
    {
        $code = (string) $request->validated('code');
        $url = ShortUrl::query()->where('short_url', $code)->first()
            ?? ShortUrl::query()->where('short_url', strtolower($code))->first();

        if ($url === null || ! $pins->matches((string) $request->validated('pin'), $url->pin_manager)) {
            return back()
                ->withInput($request->only('code'))
                ->withErrors(['pin' => 'Código ou PIN inválido.']);
        }

        $request->session()->regenerate();
        $managed->grant($url);

        return redirect()->route('manage.show', $url);
    }

    public function show(ShortUrl $shortUrl, ManagedUrls $managed): View|RedirectResponse
    {
        if (! $managed->allows($shortUrl)) {
            return redirect()->route('manage.form', ['codigo' => $shortUrl->short_url]);
        }

        return view('manage.show', [
            'url' => $shortUrl,
            'pin' => session('pin'),
            'totalVisits' => $shortUrl->visualizations()->count(),
        ]);
    }
}
