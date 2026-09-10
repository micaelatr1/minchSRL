<?php

namespace App\Services\Cotizaciones\Contracts;

use App\DTOs\PriceQuote;

interface PriceSourceInterface
{
    /** @return PriceQuote[] */
    public function fetch(): array;

    public function sourceName(): string;
}
