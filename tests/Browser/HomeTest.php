<?php

use App\Models\ShortUrl;

it('encurta uma URL pela interface e exibe o PIN uma única vez', function () {
    $page = visit('/');

    $page->assertSee('Encurtar')
        ->assertNoJavaScriptErrors()
        ->type('url', 'laravel.com/docs')
        ->press('Encurtar')
        ->assertPathBeginsWith('/gerenciar/')
        ->assertSee('Seu PIN de gerenciamento')
        ->assertSee('somente agora')
        ->assertSee('https://laravel.com/docs');

    $url = ShortUrl::sole();
    $page->assertSee($url->fullShortUrl());

    $page->navigate(route('manage.show', $url, false))
        ->assertSee('Link encurtado')
        ->assertDontSee('Seu PIN de gerenciamento');
});

it('valida a URL no front antes de enviar', function () {
    visit('/')
        ->type('url', 'isso não é url')
        ->press('Encurtar')
        ->assertPathIs('/')
        ->assertSee('Informe uma URL válida.');

    expect(ShortUrl::count())->toBe(0);
});

it('mostra o estado de carregamento ao enviar', function () {
    $page = visit('/');

    $page->type('url', 'https://example.com');
    $page->script(<<<'JS'
        document.querySelector('[data-shorten-form]').addEventListener('submit', (e) => e.preventDefault());
    JS);
    $page->press('Encurtar')
        ->assertSee('Encurtando')
        ->assertAttribute('[data-submit]', 'aria-busy', 'true');
});

it('cria link com código personalizado', function () {
    visit('/')
        ->click('Personalizar link')
        ->type('url', 'https://example.com')
        ->type('code', 'meu-link-1-compartilhado')
        ->press('Encurtar')
        ->assertPathIs('/gerenciar/meu-link-1-compartilhado')
        ->assertSee('/meu-link-1-compartilhado');
});

it('exibe erro do backend para código já usado', function () {
    ShortUrl::factory()->create(['short_url' => 'ja-existe']);

    visit('/')
        ->click('Personalizar link')
        ->type('url', 'https://example.com')
        ->type('code', 'ja-existe')
        ->press('Encurtar')
        ->assertPathIs('/')
        ->assertSee('Este código já está em uso.')
        ->assertValue('url', 'https://example.com');
});

it('é responsiva no celular', function () {
    visit('/')
        ->on()->mobile()
        ->assertSee('Encurtar')
        ->assertVisible('#url')
        ->assertNoJavaScriptErrors();
});

it('acessa um link pelo código e PIN', function () {
    $url = ShortUrl::factory()->withPin('ABCD2345')->create(['short_url' => 'aB3xYz']);

    visit('/gerenciar')
        ->type('code', 'aB3xYz')
        ->type('pin', 'abcd2345')
        ->press('Acessar')
        ->assertPathIs('/gerenciar/aB3xYz')
        ->assertSee($url->original_link);
});
