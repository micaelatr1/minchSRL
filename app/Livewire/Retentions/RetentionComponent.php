<?php

namespace App\Livewire\Retentions;

use App\Models\Retention;
use App\Models\Taxe;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class RetentionComponent extends Component
{
    use Interactions,WithPagination;

    public $selectedDate = null;

    public $type;

    public $months = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    public ?int $quantity = 2;

    public ?string $search = null;

    public function mount()
    {
        $this->selectedDate = Carbon::now()->format('Y-m');
        $this->type = 'S';
    }

    public function render()
    {
        $date = $this->selectedDate;
        $start = Carbon::createFromFormat('Y-m', $date)->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $retentions = Retention::with(['discounts:amount,retention_id', 'supplier.person'])
            ->withSum('discounts as total', 'amount')
            ->whereBetween('date', [$start, $end])
            ->where('type', $this->type)
            ->orderByDesc('code')
            ->paginate(10);

        $taxes = Taxe::where('type', $this->type)->orWhere('type', 'A')->get();

        return view('livewire.retentions.retention-component', compact('retentions', 'taxes'));
    }

    public function delete(Retention $retention)
    {
        $retention->delete();
        $this->toast()
            ->expandable(false)
            ->success('Registro eliminado', 'El impuesto fue eliminado correctamente')
            ->send();

    }

    public function consultar()
    {
        $this->toast()
            ->expandable(false)
            ->success('Consulta realizada', 'Se actualizaron los resultados de la consulta')
            ->send();
    }
}
