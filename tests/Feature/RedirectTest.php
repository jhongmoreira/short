<?php

use App\Models\ShortUrl;
use App\Models\Visualization;

it('redireciona para a URL original e registra o acesso', function () {
    $url = ShortUrl::factory()->create(['short_url' => 'aB3xYz', 'original_link' => 'https://example.com/destino']);

    $this->withHeader('User-Agent', 'Mozilla/5.0 (Linux; Android 14; Pixel 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Mobile Safari/537.36')
        ->get('/aB3xYz')
        ->assertRedirect('https://example.com/destino')
        ->assertStatus(302)
        ->assertHeader('Cache-Control', 'max-age=0, no-store, private');

    $visit = Visualization::sole();

    expect($visit->url_id)->toBe($url->url_id)
        ->and($visit->access_date_hour)->not->toBeNull()
        ->and($visit->device_info)->toBe(['os' => 'Android', 'browser' => 'Chrome', 'device' => 'mobile']);
});

it('diferencia maiúsculas em códigos gerados', function () {
    ShortUrl::factory()->create(['short_url' => 'aB3xYz']);

    $this->get('/AB3XYZ')->assertNotFound();
});

it('encontra códigos personalizados independente de maiúsculas', function () {
    ShortUrl::factory()->create(['short_url' => 'meu-link', 'original_link' => 'https://example.com']);

    $this->get('/Meu-Link')->assertRedirect('https://example.com');
});

it('retorna 404 para códigos inexistentes ou excluídos', function () {
    ShortUrl::factory()->create(['short_url' => 'apagado'])->delete();

    $this->get('/naoexiste')->assertNotFound();
    $this->get('/apagado')->assertNotFound();

    expect(Visualization::count())->toBe(0);
});
