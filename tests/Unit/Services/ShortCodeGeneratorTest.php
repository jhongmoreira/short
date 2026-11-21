<?php

use App\Services\ShortCodeGenerator;

it('gera códigos base62 com o tamanho configurado', function () {
    $generator = app(ShortCodeGenerator::class);

    foreach (range(1, 50) as $_) {
        expect($generator->generate())->toMatch('/^[A-Za-z0-9]{6}$/');
    }
});

it('identifica palavras reservadas sem diferenciar maiúsculas', function () {
    $generator = new ShortCodeGenerator('ab', 2, ['gerenciar', 'up']);

    expect($generator->isReserved('gerenciar'))->toBeTrue()
        ->and($generator->isReserved('UP'))->toBeTrue()
        ->and($generator->isReserved('meu-link'))->toBeFalse();
});

it('nunca gera um código reservado', function () {
    // Com "ab", "ba" e "bb" reservados, "aa" é o único código possível.
    $generator = new ShortCodeGenerator('ab', 2, ['ab', 'ba', 'bb']);

    foreach (range(1, 30) as $_) {
        expect($generator->generate())->toBe('aa');
    }
});
