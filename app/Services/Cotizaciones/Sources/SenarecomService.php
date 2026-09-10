<?php

namespace App\Services\Cotizaciones\Sources;

use App\DTOs\PriceQuote;
use App\Services\Cotizaciones\Contracts\PriceSourceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SenarecomService implements PriceSourceInterface
{
    private const URL = 'https://www.senarecom.gob.bo/';

    private const MAPPING = [
        'ZINC' => ['metal' => 'Zn', 'name' => 'Zinc', 'unit' => 'USD/LF'],
        'ORO' => ['metal' => 'Au', 'name' => 'Oro', 'unit' => 'USD/OT'],
        'PLATA' => ['metal' => 'Ag', 'name' => 'Plata', 'unit' => 'USD/OT'],
        'PLOMO' => ['metal' => 'Pb', 'name' => 'Plomo', 'unit' => 'USD/LF'],
    ];

    public function sourceName(): string
    {
        return 'SENARECOM';
    }

    public function fetch(): array
    {
        try {
            $response = Http::withoutVerifying()->timeout(15)->retry(2, 1000)->get(self::URL);

            if (! $response->successful()) {
                Log::warning('SENARECOM responded with {status}', [
                    'status' => $response->status(),
                ]);

                return [];
            }

            return $this->parseHtml($response->body());
        } catch (\Throwable $e) {
            Log::warning('SENARECOM request failed: {error}', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    private function parseHtml(string $html): array
    {
        $prices = [];

        foreach (self::MAPPING as $mineralName => $config) {
            $price = $this->extractMetalPrice($html, $mineralName);

            if ($price === null) {
                Log::warning('SENARECOM: no se pudo extraer precio de {metal}', [
                    'metal' => $mineralName,
                ]);

                continue;
            }

            $prices[] = new PriceQuote(
                metal: $config['metal'],
                price: $price,
                unit: $config['unit'],
                source: $this->sourceName(),
                updatedAt: Carbon::now(),
                metalName: $config['name'],
            );
        }

        return $prices;
    }

    private function extractMetalPrice(string $html, string $mineralName): ?float
    {
        $pattern = '/<div class="col-mineral">.*?'.preg_quote($mineralName).'<\/div>.*?<td class="col-precio">([\d.,]+)<\/td>/s';

        if (preg_match($pattern, $html, $matches)) {
            $rawPrice = str_replace(',', '.', str_replace('.', '', $matches[1]));

            if (is_numeric($rawPrice)) {
                return (float) $rawPrice;
            }
        }

        return null;
    }
}
