<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShortUrlRequest;
use App\Services\ManagedUrls;
use App\Services\ShortUrlCreator;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShortUrlController extends Controller
{
    public function create(): View
    {
        return view('home');
    }

    public function store(StoreShortUrlRequest $request, ShortUrlCreator $creator, ManagedUrls $managed): RedirectResponse
    {
        $created = $creator->create($request->originalLink(), $request->customCode());

        $managed->grant($created->url);

        return redirect()
            ->route('manage.show', $created->url)
            ->with('pin', $created->pin);
    }
}
