<?php

namespace App\DTOs;

use Carbon\Carbon;

class PriceQuote
{
    public function __construct(
        public readonly string $metal,
        public readonly float $price,
        public readonly string $unit,
        public readonly string $source,
        public readonly Carbon $updatedAt,
        public readonly string $metalName,
    ) {}
}
