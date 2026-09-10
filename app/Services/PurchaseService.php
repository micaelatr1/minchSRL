<?php

namespace App\Services;

use App\Models\Account;
use App\Models\KardexMovement;
use App\Models\Movement;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function createPurchase(
        Carbon $date,
        ?string $invoiceNumber,
        Supplier $supplier,
        User $user,
        array $items,
        ?int $accountId = null,
        string $paymentType = 'T',
        ?string $numberCheck = null,
        ?string $description = null
    ): Purchase {
        $total = 0;
        foreach ($items as $item) {
            $total += (float) $item['quantity'] * (float) $item['unit_cost'];
        }

        if ($accountId) {
            $this->checkSufficientBalance($accountId, $total, $date);
        }

        return DB::transaction(function () use ($date, $invoiceNumber, $supplier, $user, $items, $accountId, $paymentType, $numberCheck, $description, $total) {
            $purchase = Purchase::create([
                'date' => $date,
                'invoice_number' => $invoiceNumber,
                'supplier_id' => $supplier->id,
                'total' => 0,
                'status' => 'completed',
                'user_id' => $user->id,
            ]);

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = (float) $item['quantity'];
                $unitCost = (float) $item['unit_cost'];
                $subtotal = $quantity * $unitCost;

                $purchaseItem = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'subtotal' => $subtotal,
                ]);

                $product->refresh();
                $previousStock = (float) $product->stock;
                $previousAvgCost = (float) $product->average_cost;
                $newStock = $previousStock + $quantity;
                $newAvgCost = $this->calculateAverageCost($previousStock, $previousAvgCost, $quantity, $unitCost);

                KardexMovement::create([
                    'product_id' => $product->id,
                    'type' => 'purchase',
                    'reference_type' => PurchaseItem::class,
                    'reference_id' => $purchaseItem->id,
                    'quantity_in' => $quantity,
                    'quantity_out' => 0,
                    'unit_cost' => $unitCost,
                    'balance_quantity' => $newStock,
                    'balance_avg_cost' => $newAvgCost,
                    'balance_total_value' => $newStock * $newAvgCost,
                    'date' => $date,
                    'notes' => 'Compra #'.$purchase->id.($invoiceNumber ? ' - Factura: '.$invoiceNumber : ''),
                ]);

                $product->update([
                    'stock' => $newStock,
                    'average_cost' => $newAvgCost,
                ]);
            }

            $purchase->update(['total' => $total]);

            if ($accountId && $supplier->person_id) {
                $desc = $description ?: 'Compra #'.$purchase->id.($invoiceNumber ? ' - Factura: '.$invoiceNumber : '');

                $movement = Movement::create([
                    'date' => $date,
                    'description' => $desc,
                    'type' => 'C',
                    'amount' => $total,
                    'person_id' => $supplier->person_id,
                    'user_id' => $user->id,
                ]);

                Transaction::create([
                    'payment_type' => $paymentType,
                    'number_check' => $numberCheck,
                    'account_id' => $accountId,
                    'movement_id' => $movement->id,
                ]);

                $purchase->update(['movement_id' => $movement->id]);
            }

            return $purchase->fresh(['items.product', 'supplier.person']);
        });
    }

    public function updatePurchase(
        int $purchaseId,
        Carbon $date,
        ?string $invoiceNumber,
        Supplier $supplier,
        User $user,
        array $items,
        ?int $accountId = null,
        string $paymentType = 'T',
        ?string $numberCheck = null,
        ?string $description = null
    ): Purchase {
        return DB::transaction(function () use ($purchaseId, $date, $invoiceNumber, $supplier, $user, $items, $accountId, $paymentType, $numberCheck, $description) {
            $purchase = Purchase::with(['items.product', 'movement.transaction'])->findOrFail($purchaseId);

            $newTotal = 0;
            foreach ($items as $item) {
                $newTotal += (float) $item['quantity'] * (float) $item['unit_cost'];
            }

            // Handle movement — update, create, or delete
            if ($accountId && $purchase->movement_id) {
                $oldAmount = (float) $purchase->movement->amount;
                $balance = $this->getAccountBalance($accountId, $date);
                $availableBalance = $balance + $oldAmount;

                if ($availableBalance < $newTotal) {
                    throw new \RuntimeException(
                        "Saldo insuficiente en la cuenta contable. Saldo disponible: Bs. "
                        . number_format($availableBalance, 2)
                        . ', monto requerido: Bs. '
                        . number_format($newTotal, 2)
                    );
                }

                $desc = $description ?: 'Compra #'.$purchase->id.($invoiceNumber ? ' - Factura: '.$invoiceNumber : '');
                $purchase->movement->update([
                    'date' => $date,
                    'description' => $desc,
                    'amount' => $newTotal,
                ]);
                $purchase->movement->transaction->update([
                    'payment_type' => $paymentType,
                    'number_check' => $numberCheck,
                ]);
            } elseif ($accountId && !$purchase->movement_id && $supplier->person_id) {
                $this->checkSufficientBalance($accountId, $newTotal, $date);

                $desc = $description ?: 'Compra #'.$purchase->id.($invoiceNumber ? ' - Factura: '.$invoiceNumber : '');
                $movement = Movement::create([
                    'date' => $date,
                    'description' => $desc,
                    'type' => 'C',
                    'amount' => $newTotal,
                    'person_id' => $supplier->person_id,
                    'user_id' => $user->id,
                ]);
                Transaction::create([
                    'payment_type' => $paymentType,
                    'number_check' => $numberCheck,
                    'account_id' => $accountId,
                    'movement_id' => $movement->id,
                ]);
                $purchase->update(['movement_id' => $movement->id]);
            } elseif (!$accountId && $purchase->movement_id) {
                $purchase->movement->transaction()->delete();
                $purchase->movement->delete();
                $purchase->update(['movement_id' => null]);
            }

            // Reverse stock for old items and delete them
            foreach ($purchase->items as $item) {
                $item->product->decrement('stock', (float) $item->quantity);
                $item->kardexMovements()->delete();
                $item->delete();
            }

            // Update purchase
            $purchase->update([
                'date' => $date,
                'invoice_number' => $invoiceNumber,
                'supplier_id' => $supplier->id,
                'total' => $newTotal,
            ]);

            // Create new items + kardex
            foreach ($items as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $quantity = (float) $itemData['quantity'];
                $unitCost = (float) $itemData['unit_cost'];
                $subtotal = $quantity * $unitCost;

                $purchaseItem = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'subtotal' => $subtotal,
                ]);

                $product->refresh();
                $previousStock = (float) $product->stock;
                $previousAvgCost = (float) $product->average_cost;
                $newStock = $previousStock + $quantity;
                $newAvgCost = $this->calculateAverageCost($previousStock, $previousAvgCost, $quantity, $unitCost);

                KardexMovement::create([
                    'product_id' => $product->id,
                    'type' => 'purchase',
                    'reference_type' => PurchaseItem::class,
                    'reference_id' => $purchaseItem->id,
                    'quantity_in' => $quantity,
                    'quantity_out' => 0,
                    'unit_cost' => $unitCost,
                    'balance_quantity' => $newStock,
                    'balance_avg_cost' => $newAvgCost,
                    'balance_total_value' => $newStock * $newAvgCost,
                    'date' => $date,
                    'notes' => 'Compra #'.$purchase->id.($invoiceNumber ? ' - Factura: '.$invoiceNumber : ''),
                ]);

                $product->update([
                    'stock' => $newStock,
                    'average_cost' => $newAvgCost,
                ]);
            }

            return $purchase->fresh(['items.product', 'supplier.person', 'movement.transaction']);
        });
    }

    public function deletePurchase(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            $purchase->items->each(function (PurchaseItem $item) {
                $product = $item->product;
                $product->decrement('stock', (float) $item->quantity);

                $item->kardexMovements()->delete();
                $item->delete();
            });

            $purchase->delete();

            if ($purchase->movement_id) {
                $purchase->movement?->transaction()?->delete();
                $purchase->movement?->delete();
            }
        });
    }

    public function processSingleEntry(
        Product $product,
        float $quantity,
        float $unitCost,
        ?string $invoiceNumber,
        Supplier $supplier,
        ?string $notes,
        Carbon $date,
        User $user,
        ?int $accountId = null
    ): KardexMovement {
        $subtotal = $quantity * $unitCost;

        if ($accountId) {
            $this->checkSufficientBalance($accountId, $subtotal, $date);
        }

        return DB::transaction(function () use ($product, $quantity, $unitCost, $invoiceNumber, $supplier, $notes, $date, $user, $accountId, $subtotal) {
            $product->refresh();
            $previousStock = (float) $product->stock;
            $previousAvgCost = (float) $product->average_cost;
            $newStock = $previousStock + $quantity;
            $newAvgCost = $this->calculateAverageCost($previousStock, $previousAvgCost, $quantity, $unitCost);

            $purchase = Purchase::create([
                'date' => $date,
                'invoice_number' => $invoiceNumber,
                'supplier_id' => $supplier->id,
                'total' => $subtotal,
                'status' => 'completed',
                'user_id' => $user->id,
            ]);

            $purchaseItem = PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'subtotal' => $subtotal,
            ]);

            $kardex = KardexMovement::create([
                'product_id' => $product->id,
                'type' => 'purchase',
                'reference_type' => PurchaseItem::class,
                'reference_id' => $purchaseItem->id,
                'quantity_in' => $quantity,
                'quantity_out' => 0,
                'unit_cost' => $unitCost,
                'balance_quantity' => $newStock,
                'balance_avg_cost' => $newAvgCost,
                'balance_total_value' => $newStock * $newAvgCost,
                'date' => $date,
                'notes' => $notes ?? 'Ingreso directo: '.$product->name,
            ]);

            $product->update([
                'stock' => $newStock,
                'average_cost' => $newAvgCost,
            ]);

            if ($accountId && $supplier->person_id) {
                $movement = Movement::create([
                    'date' => $date,
                    'description' => 'Compra: '.$product->name.($invoiceNumber ? ' - Factura: '.$invoiceNumber : ''),
                    'type' => 'C',
                    'amount' => $subtotal,
                    'person_id' => $supplier->person_id,
                    'user_id' => $user->id,
                ]);

                Transaction::create([
                    'payment_type' => 'T',
                    'account_id' => $accountId,
                    'movement_id' => $movement->id,
                ]);

                $purchase->update(['movement_id' => $movement->id]);
            }

            return $kardex->fresh();
        });
    }

    private function checkSufficientBalance(int $accountId, float $requiredAmount, Carbon $date): void
    {
        $balance = $this->getAccountBalance($accountId, $date);

        if ($balance < $requiredAmount) {
            throw new \RuntimeException(
                "Saldo insuficiente en la cuenta contable. Saldo actual: Bs. "
                . number_format($balance, 2)
                . ', monto requerido: Bs. '
                . number_format($requiredAmount, 2)
            );
        }
    }

    private function getAccountBalance(int $accountId, Carbon $date): float
    {
        $monthStart = $date->copy()->startOfMonth();

        $hasBalanceInRange = Movement::query()
            ->join('transactions', 'movements.id', '=', 'transactions.movement_id')
            ->where('transactions.account_id', $accountId)
            ->where('movements.type', 'B')
            ->whereBetween('movements.date', [$monthStart, $date])
            ->exists();

        $totalDebit = Movement::query()
            ->join('transactions', 'movements.id', '=', 'transactions.movement_id')
            ->where('transactions.account_id', $accountId)
            ->whereIn('movements.type', ['D', 'B'])
            ->whereBetween('movements.date', [$monthStart, $date])
            ->sum('movements.amount');

        $totalCredit = Movement::query()
            ->join('transactions', 'movements.id', '=', 'transactions.movement_id')
            ->where('transactions.account_id', $accountId)
            ->where('movements.type', 'C')
            ->whereBetween('movements.date', [$monthStart, $date])
            ->sum('movements.amount');

        $balance = (float) $totalDebit - (float) $totalCredit;

        if (! $hasBalanceInRange) {
            $previousBalance = Movement::query()
                ->join('transactions', 'movements.id', '=', 'transactions.movement_id')
                ->where('transactions.account_id', $accountId)
                ->where('movements.type', 'B')
                ->where('movements.date', '<', $monthStart)
                ->orderBy('movements.date', 'desc')
                ->value('movements.amount');

            if ($previousBalance) {
                $balance += (float) $previousBalance;
            }
        }

        return $balance;
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
