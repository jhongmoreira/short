<?php

namespace App\Services;

/**
 * Extrai informações básicas (sistema, navegador e tipo de dispositivo)
 * de um User-Agent, sem dependências externas.
 */
class UserAgentParser
{
    private const BOTS = '/bot|crawl|spider|slurp|preview|facebookexternalhit|whatsapp|telegram|discord|curl|wget|python-requests|headless/i';

    /** @var array<string, string> padrão => nome */
    private const OPERATING_SYSTEMS = [
        '/windows phone/i' => 'Windows Phone',
        '/windows/i' => 'Windows',
        '/android/i' => 'Android',
        '/iphone|ipad|ipod/i' => 'iOS',
        '/cros/i' => 'ChromeOS',
        '/mac os x|macintosh/i' => 'macOS',
        '/linux/i' => 'Linux',
    ];

    /** @var array<string, string> padrão => nome (ordem importa) */
    private const BROWSERS = [
        '/edg(e|a|ios)?\//i' => 'Edge',
        '/opr\/|opera/i' => 'Opera',
        '/samsungbrowser/i' => 'Samsung Internet',
        '/firefox|fxios/i' => 'Firefox',
        '/chrome|crios|chromium/i' => 'Chrome',
        '/safari/i' => 'Safari',
    ];

    /**
     * @return array{os: string, browser: string, device: string}
     */
    public function parse(?string $userAgent): array
    {
        $ua = (string) $userAgent;

        return [
            'os' => $this->match($ua, self::OPERATING_SYSTEMS),
            'browser' => $this->match($ua, self::BROWSERS),
            'device' => $this->device($ua),
        ];
    }

    /**
     * @param  array<string, string>  $patterns
     */
    private function match(string $ua, array $patterns): string
    {
        foreach ($patterns as $pattern => $name) {
            if (preg_match($pattern, $ua) === 1) {
                return $name;
            }
        }

        return 'Desconhecido';
    }

    private function device(string $ua): string
    {
        return match (true) {
            $ua === '' => 'unknown',
            preg_match(self::BOTS, $ua) === 1 => 'bot',
            preg_match('/ipad|tablet|(android(?!.*mobile))/i', $ua) === 1 => 'tablet',
            preg_match('/mobi|iphone|ipod|android/i', $ua) === 1 => 'mobile',
            default => 'desktop',
        };
    }
}
