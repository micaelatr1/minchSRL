<?php

namespace App\Services\Cotizaciones;

use App\Services\Cotizaciones\Contracts\PriceSourceInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PriceService
{
    private const CACHE_KEY = 'cotizaciones';

    private const DEFAULT_TTL = 1800;

    /** @param PriceSourceInterface[] $sources */
    public function __construct(
        private readonly array $sources,
        private readonly int $ttl = self::DEFAULT_TTL,
    ) {}

    /** @return array[] */
    public function getAll(): array
    {
        return Cache::store('file')->remember(self::CACHE_KEY, $this->ttl, function () {
            return $this->fetchAll();
        });
    }

    /** @return array[] */
    public function refresh(): array
    {
        Cache::store('file')->forget(self::CACHE_KEY);

        return $this->getAll();
    }

    /** @return array[] */
    private function fetchAll(): array
    {
        $quotes = [];

        foreach ($this->sources as $source) {
            try {
                $fetched = $source->fetch();

                foreach ($fetched as $q) {
                    $quotes[] = [
                        'metal' => $q->metal,
                        'price' => $q->price,
                        'unit' => $q->unit,
                        'source' => $q->source,
                        'updatedAt' => $q->updatedAt->toIso8601String(),
                        'metalName' => $q->metalName,
                    ];
                }

                Log::info('Cotizaciones: {source} devolvio {count} precios', [
                    'source' => $source->sourceName(),
                    'count' => count($fetched),
                ]);
            } catch (\Throwable $e) {
                Log::warning('Error en fuente {source}: {error}', [
                    'source' => $source->sourceName(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $quotes;
    }
}
