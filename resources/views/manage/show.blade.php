@extends('layouts.app')

@section('title', $url->short_url)

@section('content')
    <div class="w-full max-w-2xl">
        @if($pin)
            <section aria-labelledby="pin-title"
                     class="mb-8 rounded-2xl border border-accent/40 bg-accent-soft p-5 sm:p-6">
                <h2 id="pin-title" class="font-semibold text-accent">Seu PIN de gerenciamento</h2>
                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <code data-pin class="font-mono text-2xl font-medium tracking-[0.3em] text-fg sm:text-3xl">{{ $pin }}</code>
                    <x-copy-button :text="$pin" label="Copiar PIN"/>
                </div>
                <p class="mt-3 text-sm text-fg/80">
                    Este PIN aparece <strong>somente agora</strong>. Copie e guarde em um lugar seguro:
                    sem ele você perde o acesso às informações deste link.
                </p>
            </section>
        @endif

        <h1 class="text-2xl font-semibold">Link encurtado</h1>

        <dl class="mt-6 divide-y divide-line border-y border-line">
            <div class="grid gap-1 py-4 sm:grid-cols-[10rem_1fr] sm:gap-4">
                <dt class="text-sm text-muted">URL encurtada</dt>
                <dd class="flex min-w-0 flex-wrap items-center gap-3">
                    <a href="{{ $url->fullShortUrl() }}" target="_blank" rel="noopener"
                       data-short-url class="break-all text-lg font-medium text-accent hover:underline">{{ $url->fullShortUrl() }}</a>
                    <x-copy-button :text="$url->fullShortUrl()"/>
                </dd>
            </div>
            <div class="grid gap-1 py-4 sm:grid-cols-[10rem_1fr] sm:gap-4">
                <dt class="text-sm text-muted">URL original</dt>
                <dd class="min-w-0 break-all">
                    <a href="{{ $url->original_link }}" target="_blank" rel="noopener noreferrer"
                       class="hover:text-accent">{{ $url->original_link }}</a>
                </dd>
            </div>
            <div class="grid gap-1 py-4 sm:grid-cols-[10rem_1fr] sm:gap-4">
                <dt class="text-sm text-muted">Criado em</dt>
                <dd>
                    <time datetime="{{ $url->created_at?->toIso8601String() }}">
                        {{ $url->created_at?->format('d/m/Y \à\s H:i:s') }}
                    </time>
                </dd>
            </div>
            <div class="grid gap-1 py-4 sm:grid-cols-[10rem_1fr] sm:gap-4">
                <dt class="text-sm text-muted">Acessos</dt>
                <dd data-visits>{{ $totalVisits }}</dd>
            </div>
        </dl>

        <a href="{{ route('home') }}" class="btn-accent mt-8">Encurtar outro link</a>
    </div>
@endsection
