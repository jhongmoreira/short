<?php

use App\Services\PinManager;

beforeEach(fn () => $this->pins = new PinManager('chave-secreta', 'ABC234', 8));

it('gera PIN com o tamanho e alfabeto configurados', function () {
    $pin = $this->pins->generate();

    expect($pin)->toHaveLength(8)->toMatch('/^[ABC234]{8}$/');
});

it('gera hash determinístico e não reversível', function () {
    $hash = $this->pins->hash('ABCD2345');

    expect($hash)->toBe($this->pins->hash('ABCD2345'))
        ->toHaveLength(64)
        ->not->toContain('ABCD2345');
});

it('normaliza maiúsculas, espaços e hífens ao comparar', function () {
    $hash = $this->pins->hash('ABCD2345');

    expect($this->pins->matches('abcd-2345', $hash))->toBeTrue()
        ->and($this->pins->matches(' abcd 2345 ', $hash))->toBeTrue()
        ->and($this->pins->matches('ABCD2344', $hash))->toBeFalse();
});

it('depende da chave da aplicação', function () {
    $other = new PinManager('outra-chave', 'ABC234', 8);

    expect($other->hash('ABCD2345'))->not->toBe($this->pins->hash('ABCD2345'));
});
