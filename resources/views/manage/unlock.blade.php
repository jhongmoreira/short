@extends('layouts.app')

@section('title', 'Gerenciar link')

@section('content')
    <form method="POST" action="{{ route('manage.unlock') }}" class="w-full max-w-md">
        @csrf

        <h1 class="text-2xl font-semibold">Gerenciar link</h1>
        <p class="mt-1 text-muted">Informe o código do link e o PIN recebido na criação.</p>

        <div class="mt-8 space-y-6">
            <div>
                <label for="code" class="block text-sm text-muted">Código ou URL curta</label>
                <input id="code" name="code" type="text" required autocomplete="off" spellcheck="false"
                       autocapitalize="off" placeholder="aB3xYz"
                       value="{{ old('code', $code) }}" class="field-line"
                       aria-invalid="{{ $errors->has('code') ? 'true' : 'false' }}">
                @error('code')<p role="alert" class="mt-2 text-sm text-danger">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="pin" class="block text-sm text-muted">PIN de gerenciamento</label>
                <input id="pin" name="pin" type="password" required autocomplete="off" autocapitalize="characters"
                       placeholder="••••••••" class="field-line font-mono tracking-widest"
                       aria-invalid="{{ $errors->has('pin') ? 'true' : 'false' }}"
                       @if($code !== '') autofocus @endif>
                @error('pin')<p role="alert" class="mt-2 text-sm text-danger">{{ $message }}</p>@enderror
            </div>
        </div>

        <button type="submit" class="btn-accent mt-8 w-full sm:w-auto">Acessar</button>
    </form>
@endsection

@section('footer')
    <a href="{{ route('home') }}" class="transition hover:text-accent">Encurtar um novo link</a>
@endsection
