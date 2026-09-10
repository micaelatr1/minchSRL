<?php

namespace App\Services\Cotizaciones\Sources;

use App\DTOs\PriceQuote;
use App\Services\Cotizaciones\Contracts\PriceSourceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PortalMineroService implements PriceSourceInterface
{
    private const URL = 'https://www.portalminero.com/bolsa-de-metales';

    private const MAPPING = [
        'Oro' => ['metal' => 'Au', 'name' => 'Oro', 'unit' => 'USD/OT', 'factor' => 1],
        'Plata' => ['metal' => 'Ag', 'name' => 'Plata', 'unit' => 'USD/OT', 'factor' => 1],
        'Zinc' => ['metal' => 'Zn', 'name' => 'Zinc', 'unit' => 'USD/LB', 'factor' => 0.01],
        'Plomo' => ['metal' => 'Pb', 'name' => 'Plomo', 'unit' => 'USD/LB', 'factor' => 0.01],
    ];

    public function sourceName(): string
    {
        return 'PortalMinero';
    }

    public function fetch(): array
    {
        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->retry(2, 1000)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'es-BO,es-419;q=0.9,es;q=0.8,en;q=0.7',
                ])
                ->get(self::URL);

            if (! $response->successful()) {
                Log::warning('PortalMinero responded with {status}', [
                    'status' => $response->status(),
                ]);

                return [];
            }

            return $this->parseHtml($response->body());
        } catch (\Throwable $e) {
            Log::warning('PortalMinero request failed: {error}', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    private function parseHtml(string $html): array
    {
        $prices = [];

        foreach (self::MAPPING as $metalName => $config) {
            $price = $this->extractMetalPrice($html, $metalName);

            if ($price === null) {
                Log::warning('PortalMinero: no se pudo extraer precio de {metal}', [
                    'metal' => $metalName,
                ]);

                continue;
            }

            $prices[] = new PriceQuote(
                metal: $config['metal'],
                price: $price * $config['factor'],
                unit: $config['unit'],
                source: $this->sourceName(),
                updatedAt: Carbon::now(),
                metalName: $config['name'],
            );
        }

        return $prices;
    }

    private function extractMetalPrice(string $html, string $metalName): ?float
    {
        $pattern = '/<span class="pmtab-metal">.*?'.preg_quote($metalName).'<span class="pmtab-u">.*?<\/span><\/span>.*?<\/td>.*?<td>.*?<\/td>.*?<td class="grp pmtab-num">([\d.]+)<\/td>.*?<td class="pmtab-num">([\d.]+)<\/td>/s';

        if (preg_match($pattern, $html, $matches)) {
            $price = (float) $matches[2];

            if ($price > 0) {
                return $price;
            }
        }

        return null;
    }
}
