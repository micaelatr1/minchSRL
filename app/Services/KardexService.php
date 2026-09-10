<?php

namespace App\Services;

use App\Models\KardexMovement;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class KardexService
{
    public function processEntry(
        Product $product,
        float $quantity,
        float $unitCost,
        string $documentNumber,
        Supplier $supplier,
        string $description,
        Carbon $date,
        User $user,
        ?int $accountId = null
    ): KardexMovement {
        return app(PurchaseService::class)->processSingleEntry(
            $product, $quantity, $unitCost, $documentNumber,
            $supplier, $description, $date, $user, $accountId
        );
    }

    public function processConsumption(
        Product $product,
        float $quantity,
        string $description,
        Carbon $date,
        User $user,
        ?float $unitCost = null
    ): KardexMovement {
        return DB::transaction(function () use ($product, $quantity, $description, $date, $unitCost) {
            $product->refresh();

            $previousStock = (float) $product->stock;
            $outQuantity = abs($quantity);
            $cost = $unitCost ?? (float) $product->average_cost;
            $totalCost = $outQuantity * $cost;
            $newStock = $previousStock - $outQuantity;

            if ($newStock < 0) {
                throw new \RuntimeException('Stock insuficiente para el consumo solicitado.');
            }

            $kardex = KardexMovement::create([
                'product_id' => $product->id,
                'type' => 'adjustment_out',
                'quantity_in' => 0,
                'quantity_out' => $outQuantity,
                'unit_cost' => $cost,
                'balance_quantity' => $newStock,
                'balance_avg_cost' => $cost,
                'balance_total_value' => $newStock * $cost,
                'date' => $date,
                'notes' => $description,
            ]);

            $product->update(['stock' => $newStock]);

            return $kardex->fresh();
        });
    }

    public function processAdjustment(
        Product $product,
        float $quantity,
        ?float $unitCost,
        string $description,
        Carbon $date,
        User $user
    ): KardexMovement {
        return DB::transaction(function () use ($product, $quantity, $unitCost, $description, $date) {
            $product->refresh();

            $previousStock = (float) $product->stock;
            $previousAvgCost = (float) $product->average_cost;
            $newStock = $previousStock + $quantity;

            if ($newStock < 0) {
                throw new \RuntimeException('Stock insuficiente para el ajuste solicitado.');
            }

            $isPositive = $quantity > 0;
            $absQty = abs($quantity);

            if ($isPositive) {
                $cost = $unitCost ?? $previousAvgCost;
                $newAvgCost = $this->calculateAverageCost($previousStock, $previousAvgCost, $quantity, $cost);
            } else {
                $cost = $previousAvgCost;
                $newAvgCost = $previousAvgCost;
            }

            $kardex = KardexMovement::create([
                'product_id' => $product->id,
                'type' => $isPositive ? 'adjustment_in' : 'adjustment_out',
                'quantity_in' => $isPositive ? $absQty : 0,
                'quantity_out' => $isPositive ? 0 : $absQty,
                'unit_cost' => $cost,
                'balance_quantity' => $newStock,
                'balance_avg_cost' => $newAvgCost,
                'balance_total_value' => $newStock * $newAvgCost,
                'date' => $date,
                'notes' => $description,
            ]);

            $product->update([
                'stock' => $newStock,
                'average_cost' => $newAvgCost,
            ]);

            return $kardex->fresh();
        });
    }

    public function getKardex(Product $product, ?Carbon $dateFrom = null, ?Carbon $dateTo = null): Collection
    {
        $query = $product->kardexMovements()
            ->orderBy('date')
            ->orderBy('id');

        if ($dateFrom) {
            $query->where('date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('date', '<=', $dateTo);
        }

        return $query->get();
    }

    public function recalculateFrom(Product $product, Carbon $date): void
    {
        DB::transaction(function () use ($product, $date) {
            $previousMovements = KardexMovement::where('product_id', $product->id)
                ->where('date', '<', $date)
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $runningStock = $previousMovements ? (float) $previousMovements->balance_quantity : 0;
            $runningAvgCost = $previousMovements ? (float) $previousMovements->balance_avg_cost : 0;

            $movements = KardexMovement::where('product_id', $product->id)
                ->where('date', '>=', $date)
                ->orderBy('date')
                ->orderBy('id')
                ->get();

            foreach ($movements as $kardex) {
                $qtyIn = (float) $kardex->quantity_in;
                $qtyOut = (float) $kardex->quantity_out;

                if ($qtyIn > 0) {
                    $unitCost = (float) $kardex->unit_cost;
                    $runningAvgCost = $this->calculateAverageCost($runningStock, $runningAvgCost, $qtyIn, $unitCost);
                }

                $runningStock += $qtyIn - $qtyOut;

                $kardex->update([
                    'balance_quantity' => $runningStock,
                    'balance_avg_cost' => $runningAvgCost,
                    'balance_total_value' => $runningStock * $runningAvgCost,
                ]);
            }

            $product->update([
                'stock' => $runningStock,
                'average_cost' => $runningAvgCost,
            ]);
        });
    }

    private function calculateAverageCost(
        float $currentStock,
        float $currentAvgCost,
        float $entryQuantity,
        float $entryUnitCost
    ): float {
        if ($currentStock + $entryQuantity <= 0) {
            return $entryUnitCost;
        }

        $totalValue = ($currentStock * $currentAvgCost) + ($entryQuantity * $entryUnitCost);
        $totalQty = $currentStock + $entryQuantity;

        return $totalValue / $totalQty;
    }
}
