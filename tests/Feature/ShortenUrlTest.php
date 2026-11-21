<?php

use App\Models\ShortUrl;
use App\Services\PinManager;

it('exibe a tela inicial com o input e o botão', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Cole sua URL aqui')
        ->assertSee('Encurtar');
});

it('encurta uma URL válida e redireciona para a tela do link com o PIN', function () {
    $response = $this->post('/encurtar', ['url' => 'https://laravel.com/docs/12.x']);

    $url = ShortUrl::sole();

    $response->assertRedirect(route('manage.show', $url))
        ->assertSessionHas('pin');

    $pin = session('pin');

    expect($url->original_link)->toBe('https://laravel.com/docs/12.x')
        ->and($url->short_url)->toMatch('/^[A-Za-z0-9]{6}$/')
        ->and($url->pin_manager)->not->toBe($pin)
        ->and(app(PinManager::class)->matches($pin, $url->pin_manager))->toBeTrue();

    $this->get(route('manage.show', $url))
        ->assertOk()
        ->assertSee($pin)
        ->assertSee('somente agora', false)
        ->assertSee($url->fullShortUrl())
        ->assertSee('https://laravel.com/docs/12.x')
        ->assertSee($url->created_at?->format('d/m/Y'));
});

it('mostra o PIN apenas uma vez', function () {
    $this->post('/encurtar', ['url' => 'https://example.com']);
    $url = ShortUrl::sole();
    $pin = session('pin');

    $this->get(route('manage.show', $url))->assertSee($pin);
    $this->get(route('manage.show', $url))->assertOk()->assertDontSee($pin);
});

it('adiciona https quando o protocolo não é informado', function () {
    $this->post('/encurtar', ['url' => 'example.com/pagina']);

    expect(ShortUrl::sole()->original_link)->toBe('https://example.com/pagina');
});

it('permite a mesma URL original várias vezes com códigos e PINs diferentes', function () {
    $this->post('/encurtar', ['url' => 'https://example.com']);
    $this->post('/encurtar', ['url' => 'https://example.com']);

    $urls = ShortUrl::all();

    expect($urls)->toHaveCount(2)
        ->and($urls[0]->short_url)->not->toBe($urls[1]->short_url)
        ->and($urls[0]->pin_manager)->not->toBe($urls[1]->pin_manager);
});

it('rejeita URLs inválidas', function (string $invalid) {
    $this->from('/')->post('/encurtar', ['url' => $invalid])
        ->assertRedirect('/')
        ->assertSessionHasErrors('url');

    expect(ShortUrl::count())->toBe(0);
})->with([
    'vazia' => [''],
    'texto solto' => ['isso não é url'],
    'sem domínio' => ['https://abc'],
    'protocolo ftp' => ['ftp://example.com/arquivo'],
    'javascript' => ['javascript:alert(1)'],
]);

it('rejeita URLs maiores que o limite', function () {
    $this->post('/encurtar', ['url' => 'https://example.com/'.str_repeat('a', 2100)])
        ->assertSessionHasErrors('url');
});

it('aceita um código personalizado e o guarda em minúsculas', function () {
    $this->post('/encurtar', ['url' => 'https://example.com', 'code' => 'Meu-Link-1-Compartilhado'])
        ->assertSessionHasNoErrors();

    expect(ShortUrl::sole()->short_url)->toBe('meu-link-1-compartilhado');
});

it('rejeita códigos personalizados inválidos', function (string $code) {
    $this->post('/encurtar', ['url' => 'https://example.com', 'code' => $code])
        ->assertSessionHasErrors('code');
})->with([
    'curto demais' => ['ab'],
    'caracteres especiais' => ['meu_link!'],
    'hífen no início' => ['-meu-link'],
    'hífen duplo' => ['meu--link'],
    'longo demais' => [str_repeat('a', 51)],
    'palavra reservada' => ['gerenciar'],
    'rota de saúde' => ['up'],
]);

it('rejeita código personalizado já usado, inclusive por link excluído', function () {
    ShortUrl::factory()->create(['short_url' => 'ja-existe']);
    ShortUrl::factory()->create(['short_url' => 'excluido'])->delete();

    $this->post('/encurtar', ['url' => 'https://example.com', 'code' => 'ja-existe'])
        ->assertSessionHasErrors(['code' => 'Este código já está em uso. Escolha outro.']);

    $this->post('/encurtar', ['url' => 'https://example.com', 'code' => 'excluido'])
        ->assertSessionHasErrors('code');
});

it('limita a quantidade de links criados por minuto', function () {
    config(['short.shorten_per_minute' => 2]);

    $this->post('/encurtar', ['url' => 'https://example.com']);
    $this->post('/encurtar', ['url' => 'https://example.com']);
    $this->post('/encurtar', ['url' => 'https://example.com'])->assertTooManyRequests();
});
