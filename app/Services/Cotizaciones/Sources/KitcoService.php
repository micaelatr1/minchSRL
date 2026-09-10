<?php

namespace App\Services\Cotizaciones\Sources;

use App\DTOs\PriceQuote;
use App\Services\Cotizaciones\Contracts\PriceSourceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KitcoService implements PriceSourceInterface
{
    private const PAGES = [
        'https://www.kitco.com/charts/silver' => ['metal' => 'Ag', 'name' => 'Plata', 'unit' => 'USD/OT'],
        'https://www.kitco.com/charts/gold' => ['metal' => 'Au', 'name' => 'Oro', 'unit' => 'USD/OT'],
    ];

    public function sourceName(): string
    {
        return 'Kitco';
    }

    public function fetch(): array
    {
        $quotes = [];

        foreach (self::PAGES as $url => $config) {
            try {
                $response = Http::withoutVerifying()
                    ->timeout(15)
                    ->retry(2, 1000)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                        'Accept-Language' => 'es-BO,es-419;q=0.9,es;q=0.8,en;q=0.7',
                    ])
                    ->get($url);

                if (! $response->successful()) {
                    Log::warning('Kitco {metal} responded with {status}', [
                        'metal' => $config['metal'],
                        'status' => $response->status(),
                    ]);

                    continue;
                }

                $body = $response->body();
                $price = $this->extractFromJson($body) ?? $this->extractFromHtml($body);

                if ($price === null) {
                    Log::warning('Kitco {metal}: no se pudo extraer precio', [
                        'metal' => $config['metal'],
                    ]);

                    continue;
                }

                $quotes[] = new PriceQuote(
                    metal: $config['metal'],
                    price: $price,
                    unit: $config['unit'],
                    source: $this->sourceName(),
                    updatedAt: Carbon::now(),
                    metalName: $config['name'],
                );
            } catch (\Throwable $e) {
                Log::warning('Kitco {metal} failed: {error}', [
                    'metal' => $config['metal'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $quotes;
    }

    private function extractFromJson(string $body): ?float
    {
        if (! preg_match('/<script id="__NEXT_DATA__" type="application\/json">(.*?)<\/script>/s', $body, $m)) {
            return null;
        }

        $data = json_decode($m[1], true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        $queries = $data['props']['pageProps']['dehydratedState']['queries'] ?? [];

        foreach ($queries as $query) {
            $result = $query['state']['data']['GetMetalQuoteV3']['results'][0] ?? null;

            if ($result && isset($result['bid'])) {
                return (float) $result['bid'];
            }
        }

        return null;
    }

    private function extractFromHtml(string $body): ?float
    {
        if (preg_match('/<span[^>]*>\s*([\d,]+\.\d+)\s*<\/span>/', $body, $m)) {
            $cleaned = str_replace(',', '', $m[1]);

            if (is_numeric($cleaned)) {
                return (float) $cleaned;
            }
        }

        if (preg_match('/Bid\s*([\d,]+\.\d+)/i', $body, $m)) {
            return (float) str_replace(',', '', $m[1]);
        }

        return null;
    }
}
