@extends('layouts.app')

@section('title', 'Encurtar link')

@section('content')
    @php($customOpen = old('code') !== null || $errors->has('code'))

    <form method="POST" action="{{ route('short-urls.store') }}" novalidate data-shorten-form
          class="w-full max-w-2xl">
        @csrf

        <h1 class="sr-only">Encurtar um link</h1>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label for="url" class="sr-only">URL para encurtar</label>
                <input id="url" name="url" type="text" inputmode="url" autocomplete="url" autofocus
                       spellcheck="false" autocapitalize="off"
                       placeholder="Cole sua URL aqui"
                       value="{{ old('url') }}"
                       aria-describedby="url-error"
                       aria-invalid="{{ $errors->has('url') ? 'true' : 'false' }}"
                       class="field-line">
            </div>

            <button type="submit" data-submit class="btn-accent shrink-0">
                <x-spinner data-spinner hidden/>
                <span data-label>Encurtar</span>
            </button>
        </div>

        <p id="url-error" role="alert" class="mt-2 text-sm text-danger" @unless($errors->has('url')) hidden @endunless>
            {{ $errors->first('url') }}
        </p>

        <div class="mt-6">
            <button type="button" data-toggle-custom aria-controls="custom-box"
                    aria-expanded="{{ $customOpen ? 'true' : 'false' }}"
                    class="text-sm text-muted underline-offset-4 transition hover:text-accent hover:underline">
                Personalizar link
            </button>

            <div id="custom-box" data-custom class="mt-3" @unless($customOpen) hidden @endunless>
                <label for="code" class="mb-1 block text-sm text-muted">Código próprio (opcional)</label>
                <div class="flex items-end gap-1">
                    <span class="shrink-0 pb-3 text-lg text-muted">{{ preg_replace('#^https?://#', '', url('/')) }}/</span>
                    <input id="code" name="code" type="text" autocomplete="off" spellcheck="false" autocapitalize="off"
                           maxlength="{{ config('short.custom_code_max') }}"
                           placeholder="meu-link"
                           value="{{ old('code') }}"
                           aria-describedby="code-error"
                           aria-invalid="{{ $errors->has('code') ? 'true' : 'false' }}"
                           class="field-line min-w-0">
                </div>
                <p id="code-error" role="alert" class="mt-2 text-sm text-danger" @unless($errors->has('code')) hidden @endunless>
                    {{ $errors->first('code') }}
                </p>
            </div>
        </div>
    </form>
@endsection

@section('footer')
    <a href="{{ route('manage.form') }}" class="transition hover:text-accent">Gerenciar um link com PIN</a>
@endsection
