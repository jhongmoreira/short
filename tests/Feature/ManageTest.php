<?php

use App\Models\ShortUrl;
use App\Models\Visualization;

it('pede código e PIN para acessar um link de outra sessão', function () {
    $url = ShortUrl::factory()->create(['short_url' => 'aB3xYz']);

    $this->get(route('manage.show', $url))
        ->assertRedirect(route('manage.form', ['codigo' => 'aB3xYz']));

    $this->get(route('manage.form', ['codigo' => 'aB3xYz']))
        ->assertOk()
        ->assertSee('value="aB3xYz"', false);
});

it('libera o acesso com código e PIN corretos', function () {
    $url = ShortUrl::factory()->withPin('ABCD2345')->create(['short_url' => 'aB3xYz']);
    Visualization::factory()->count(3)->for($url, 'shortUrl')->create();

    $this->post(route('manage.unlock'), ['code' => 'aB3xYz', 'pin' => 'abcd2345'])
        ->assertRedirect(route('manage.show', $url));

    $this->get(route('manage.show', $url))
        ->assertOk()
        ->assertSee($url->original_link)
        ->assertSeeInOrder(['Acessos', '3'])
        ->assertDontSee('ABCD2345');
});

it('aceita a URL curta completa no lugar do código', function () {
    $url = ShortUrl::factory()->withPin('ABCD2345')->create(['short_url' => 'aB3xYz']);

    $this->post(route('manage.unlock'), ['code' => url('aB3xYz'), 'pin' => 'ABCD2345'])
        ->assertRedirect(route('manage.show', $url));
});

it('recusa PIN incorreto ou PIN de outro link', function () {
    ShortUrl::factory()->withPin('ABCD2345')->create(['short_url' => 'primeiro']);
    ShortUrl::factory()->withPin('ZZZZ9999')->create(['short_url' => 'segundo']);

    $this->from(route('manage.form'))
        ->post(route('manage.unlock'), ['code' => 'primeiro', 'pin' => 'ZZZZ9999'])
        ->assertRedirect(route('manage.form'))
        ->assertSessionHasErrors(['pin' => 'Código ou PIN inválido.']);

    $this->post(route('manage.unlock'), ['code' => 'naoexiste', 'pin' => 'ABCD2345'])
        ->assertSessionHasErrors('pin');
});

it('exige código e PIN', function () {
    $this->post(route('manage.unlock'), [])
        ->assertSessionHasErrors(['code', 'pin']);
});

it('limita tentativas de PIN', function () {
    config(['short.pin_attempts_per_minute' => 3]);
    ShortUrl::factory()->create(['short_url' => 'alvo']);

    foreach (range(1, 3) as $_) {
        $this->post(route('manage.unlock'), ['code' => 'alvo', 'pin' => 'ERRADO12'])->assertRedirect();
    }

    $this->post(route('manage.unlock'), ['code' => 'alvo', 'pin' => 'ERRADO12'])->assertTooManyRequests();
});

it('retorna 404 ao gerenciar link inexistente', function () {
    $this->get('/gerenciar/naoexiste')->assertNotFound();
});
