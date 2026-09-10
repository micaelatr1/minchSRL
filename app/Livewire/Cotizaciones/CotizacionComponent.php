<?php

namespace App\Livewire\Cotizaciones;

use App\Services\Cotizaciones\PriceService;
use App\Services\Cotizaciones\Sources\KitcoService;
use App\Services\Cotizaciones\Sources\PortalMineroService;
use App\Services\Cotizaciones\Sources\SenarecomService;
use Livewire\Component;

class CotizacionComponent extends Component
{
    public ?string $filterMetal = null;

    public array $prices = [];

    public ?string $lastUpdate = null;

    public function mount(): void
    {
        $this->loadPrices();
    }

    public function refresh(): void
    {
        $this->loadPrices(refresh: true);
    }

    public function getFilteredPricesProperty(): array
    {
        return $this->filterMetal
            ? array_values(array_filter(
                $this->prices,
                fn (array $p) => $p['metal'] === $this->filterMetal,
            ))
            : $this->prices;
    }

    public function getAvailableMetalsProperty(): array
    {
        $metals = [];

        foreach ($this->prices as $quote) {
            $metals[$quote['metal']] = $quote['metalName'];
        }

        return $metals;
    }

    private function loadPrices(bool $refresh = false): void
    {
        $service = new PriceService(
            sources: [new KitcoService, new SenarecomService, new PortalMineroService],
        );

        $this->prices = $refresh ? $service->refresh() : $service->getAll();
        $this->lastUpdate = now()->isoFormat('DD/MM/YYYY HH:mm');
    }

    public function render()
    {
        return view('livewire.cotizaciones.cotizacion-component');
    }
}
