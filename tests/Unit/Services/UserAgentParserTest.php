<?php

use App\Services\UserAgentParser;

it('identifica sistema, navegador e dispositivo', function (string $ua, string $os, string $browser, string $device) {
    expect((new UserAgentParser)->parse($ua))->toBe([
        'os' => $os,
        'browser' => $browser,
        'device' => $device,
    ]);
})->with([
    'Chrome no Android' => [
        'Mozilla/5.0 (Linux; Android 14; Pixel 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Mobile Safari/537.36',
        'Android', 'Chrome', 'mobile',
    ],
    'Safari no iPhone' => [
        'Mozilla/5.0 (iPhone; CPU iPhone OS 18_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.1 Mobile/15E148 Safari/604.1',
        'iOS', 'Safari', 'mobile',
    ],
    'Safari no iPad' => [
        'Mozilla/5.0 (iPad; CPU OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
        'iOS', 'Safari', 'tablet',
    ],
    'Edge no Windows' => [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36 Edg/130.0.0.0',
        'Windows', 'Edge', 'desktop',
    ],
    'Firefox no Linux' => [
        'Mozilla/5.0 (X11; Linux x86_64; rv:132.0) Gecko/20100101 Firefox/132.0',
        'Linux', 'Firefox', 'desktop',
    ],
    'Chrome no macOS' => [
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36',
        'macOS', 'Chrome', 'desktop',
    ],
    'Samsung Internet em tablet' => [
        'Mozilla/5.0 (Linux; Android 13; SM-X700) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/23.0 Chrome/115.0.0.0 Safari/537.36',
        'Android', 'Samsung Internet', 'tablet',
    ],
    'Robô do Google' => [
        'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        'Desconhecido', 'Desconhecido', 'bot',
    ],
]);

it('lida com user-agent vazio', function () {
    expect((new UserAgentParser)->parse(null))->toBe([
        'os' => 'Desconhecido',
        'browser' => 'Desconhecido',
        'device' => 'unknown',
    ]);
});
