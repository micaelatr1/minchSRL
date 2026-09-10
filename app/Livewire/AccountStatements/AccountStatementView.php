<?php

namespace App\Livewire\AccountStatements;

use App\Models\Movement;
use App\Services\AccountStatementService;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class AccountStatementView extends Component
{
    use Interactions, WithPagination;

    public string $holderType;

    public int $holderId;

    public $id;

    public $amount;

    public $type;

    public $date;

    public $doc;

    public $vol;

    public $description;

    public function mount(string $type, int $id): void
    {
        abort_unless(in_array($type, AccountStatementService::TYPES, true), 404);

        $this->holderType = $type;
        $this->holderId = $id;
        $this->id = 0;
        $this->date = now()->format('Y-m-d');
    }

    public function render()
    {
        $service = app(AccountStatementService::class);
        $holder = $service->resolve($this->holderType, $this->holderId);
        $totals = $service->totalsForPerson($holder->person_id);

        $holder->amountD = number_format($totals['amountD'], 2, '.', ',');
        $holder->amountC = number_format($totals['amountC'], 2, '.', ',');
        $holder->balance = number_format($totals['balance'], 2, '.', ',');

        $accountStatements = $service
            ->movementsForPerson($holder->person_id)
            ->paginate(10);

        $holderLabel = $service->holderLabel($this->holderType);

        return view('livewire.account-statements.account-statement-view', compact(
            'accountStatements',
            'holder',
            'holderLabel'
        ));
    }

    public function store(): void
    {
        $this->validate($this->movementRules());

        $holder = app(AccountStatementService::class)->resolve($this->holderType, $this->holderId);

        Movement::create([
            'amount' => $this->amount,
            'type' => $this->type,
            'date' => $this->date,
            'description' => $this->description,
            'number_vol' => $this->vol,
            'person_id' => $holder->person_id,
            'user_id' => auth()->id(),
        ]);

        $this->toast()
            ->expandable(false)
            ->success('Movimiento registrado', 'El movimiento fue agregado al estado de cuenta')
            ->send();

        $this->clear();
    }

    #[On('load::movement')]
    public function edit(Movement $movement): void
    {
        /* abort_unless($this->canManageMovement($movement), 403); */

        $this->id = $movement->id;
        $this->amount = $movement->amount;
        $this->type = $movement->type;
        $this->date = $movement->date;
        $this->doc = null;
        $this->vol = $movement->number_vol;
        $this->description = $movement->description;
        $this->js("window.\$tsui.open.modal('crud-modal')");
    }

    public function update(): void
    {
        $this->validate($this->movementRules());

        $movement = Movement::findOrFail($this->id);
        /* abort_unless($this->canManageMovement($movement), 403); */

        $movement->update([
            'amount' => $this->amount,
            'type' => $this->type,
            'date' => $this->date,
            'description' => $this->description,
            'number_vol' => $this->vol,
        ]);

        $this->toast()
            ->expandable(false)
            ->success('Movimiento actualizado', 'El registro fue modificado correctamente')
            ->send();

        $this->clear();
    }

    public function delete(Movement $movement): void
    {
        abort_unless($this->canManageMovement($movement), 403);

        $movement->delete();

        $this->toast()
            ->expandable(false)
            ->success('Registro eliminado', 'El movimiento fue eliminado correctamente')
            ->send();
    }

    public function clear(): void
    {
        $this->dispatch('close-modal');
        $this->resetValidation();
        $this->reset(['amount', 'type', 'description', 'doc', 'vol', 'id']);
        $this->date = now()->format('Y-m-d');
    }

    private function canManageMovement(Movement $movement): bool
    {
        if ($movement->type === 'B') {
            return false;
        }

        $movement->loadMissing(['box', 'transaction']);
        $holder = app(AccountStatementService::class)->resolve($this->holderType, $this->holderId);

        return $movement->person_id === $holder->person_id
            && $movement->box === null
            && $movement->transaction === null;
    }

    private function movementRules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:D,C',
            'date' => 'required|date|before_or_equal:today',
            'description' => 'required|string|max:255',
            'doc' => 'nullable|string|max:20',
            'vol' => 'nullable|string|max:20',
        ];
    }
}
