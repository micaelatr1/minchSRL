<?php

namespace App\Livewire\Liquidations;

use App\Models\Liquidation;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class LiquidationForm extends Component
{
    use Interactions;

    public $id = 0;

    public $metal = 'zn';

    public $ci;

    public $customer_id;

    public $full_name;

    public $nim;

    public $nit;

    public $concession;

    public $mine;

    public $municipality;

    public $cooperative_name;

    public $lote;

    public $date;

    public $lab_quimico;

    public $number_lab;

    public $codigo;

    // Metal quotations (DB-bound)
    public $quincenal_zn = 0;

    public $quincenal_pb = 0;

    public $market_zn = 0;

    public $market_pb = 0;

    public $quincenal_ag = 0;

    public $market_ag = 0;

    // Metal quotations (Alpine-bound, @entangle in view)
    public $NIM = 0;        // quincenal_zn

    public $nimPb = 0;      // quincenal_pb

    public $Zn = 0;         // market_zn

    public $Pb = 0;         // market_pb

    public $agNIM = 0;      // quincenal_ag

    public $AgUSD = 0;      // market_ag

    // Weights and grades (DB & Alpine shared)
    public $tmh = 0;

    public $h2o = 0;

    public $merma = 0;

    public $dm = 0;

    public $zinc_grade = 0;

    public $lead_grade = 0;

    public $maquila = 0;

    public $base = 0;

    public $zinc = 0;       // zinc_grade

    public $plomo = 0;      // lead_grade

    // Penalties - contaminants (DB & Alpine)
    public $as_pct = 0;

    public $sb_pct = 0;

    public $fe_pct = 0;

    public $sio2_pct = 0;

    public $sn_pct = 0;

    public $As = 0;         // as_pct

    public $Sb = 0;         // sb_pct

    public $Fe = 0;         // fe_pct

    public $SiO2 = 0;       // sio2_pct

    public $Sn = 0;         // sn_pct

    // Penalty thresholds (DB & Alpine)
    public $p_as = 0;

    public $p_as_usd = 0;

    public $p_as_pct = 0;

    public $p_sb = 0;

    public $p_sb_usd = 0;

    public $p_sb_pct = 0;

    public $p_fe = 0;

    public $p_fe_usd = 0;

    public $p_fe_pct = 0;

    public $p_sio2 = 0;

    public $p_sio2_usd = 0;

    public $p_sio2_pct = 0;

    public $p_sn = 0;

    public $p_sn_usd = 0;

    public $p_sn_pct = 0;

    public $PAs = 0;        // p_as

    public $PAsUSD = 0;     // p_as_usd

    public $PAsp = 0;       // p_as_pct

    public $PSb = 0;        // p_sb

    public $PSbUSD = 0;     // p_sb_usd

    public $PSbp = 0;       // p_sb_pct

    public $PFe = 0;        // p_fe

    public $PFeUSD = 0;     // p_fe_usd

    public $PFep = 0;       // p_fe_pct

    public $PSiO2 = 0;      // p_sio2

    public $PSiO2USD = 0;   // p_sio2_usd

    public $PSiO2p = 0;     // p_sio2_pct

    public $PSn = 0;        // p_sn

    public $PSnUSD = 0;     // p_sn_usd

    public $PSnp = 0;       // p_sn_pct

    // Treatment (DB & Alpine)
    public $refinacion = 0;

    public $basePorcentaje = 0;     // base_percentage

    // Expenses (DB & Alpine)
    public $flete = 0;

    public $rollback = 0;

    public $tc = 0;

    public $remesaPct = 0;          // remesa_pct

    // Royalties (DB & Alpine)
    public $regaliaZn = 0;          // regalia_zn

    public $regaliaPb = 0;          // regalia_pb

    public $regaliaAg = 0;          // regalia_ag

    public $factorRegalia = 0;      // factor_regalia

    // Contributions (DB & Alpine)
    public $cns = 0;                // cns_pct

    public $comibol = 0;            // comibol_pct

    public $fedecomin = 0;          // fedecomin_pct

    public $fencomin = 0;           // fencomin_pct

    public $aporteCoop = 0;         // aporte_coop_pct

    public function mount($id = 0)
    {
        $this->date = now()->format('Y-m-d');

        if ($id) {
            $liquidation = Liquidation::with('customer')->findOrFail($id);
            $this->id = $liquidation->id;
            $this->metal = $liquidation->metal;
            $this->customer_id = $liquidation->customer_id;
            $this->full_name = $liquidation->full_name;
            $this->nim = $liquidation->nim;
            $this->nit = $liquidation->nit;
            $this->concession = $liquidation->concession;
            $this->mine = $liquidation->mine;
            $this->municipality = $liquidation->municipality;
            $this->cooperative_name = $liquidation->cooperative_name;
            $this->lote = $liquidation->lote;
            $this->date = $liquidation->date->format('Y-m-d');
            $this->lab_quimico = $liquidation->lab_quimico;
            $this->number_lab = $liquidation->number_lab;
            $this->codigo = $liquidation->codigo;

            $this->setAlpineProperties($liquidation);
            $this->ci = $liquidation->customer?->person?->ci;
        } else {
            $this->setDefaults();
        }
    }

    private function setDefaults(): void
    {
        $this->merma = 1;
        $this->basePorcentaje = 0.15;
        $this->PAs = 0.5;
        $this->PFe = 8;
        $this->PSb = 0.5;
        $this->PSiO2 = 3;
        $this->PSn = 0.5;
        $this->regaliaAg = 3.6;
        $this->factorRegalia = 6.96;
        $this->remesaPct = 0.8;
        $this->cns = 1.8;
        $this->fedecomin = 1;
        $this->fencomin = 0.4;
        $this->aporteCoop = 0;
        $this->refinacion = 0.05;
        $this->agNIM = 76.38;

        if ($this->metal === 'pb') {
            $this->tmh = 35.920;
            $this->h2o = 10.66815;
            $this->dm = 24.21;
            $this->base = 2000;
            $this->maquila = 0;
            $this->Sb = 1.30;
            $this->Fe = 6.20;
            $this->SiO2 = 3;
            $this->Sn = 0;
            $this->As = 1.30;
            $this->PAsUSD = 3.5;
            $this->PSbUSD = 3.5;
            $this->PFeUSD = 3.5;
            $this->PSiO2USD = 3.5;
            $this->PSnUSD = 3.5;
            $this->PAsp = 0.1;
            $this->PFep = 1;
            $this->PSbp = 0.1;
            $this->PSiO2p = 1;
            $this->PSnp = 0.1;
            $this->AgUSD = 73.50;
            $this->tc = 9.10;
            $this->flete = 180;
            $this->rollback = 38;
            $this->comibol = 0;
            $this->remesaPct = 0.6;
            $this->nimPb = 0.88;
            $this->plomo = 52.88;
            $this->Pb = 1936;
            $this->regaliaPb = 3;
        } else {
            $this->tmh = 22.720;
            $this->h2o = 11.45;
            $this->dm = 6.91;
            $this->base = 3000;
            $this->maquila = 90;
            $this->Sb = 0.1;
            $this->Fe = 9.5;
            $this->SiO2 = 5;
            $this->Sn = 0;
            $this->As = 0.1;
            $this->PAsUSD = 3;
            $this->PSbUSD = 3;
            $this->PFeUSD = 3;
            $this->PSiO2USD = 3;
            $this->PSnUSD = 3;
            $this->PAsp = 0.1;
            $this->PFep = 1;
            $this->PSbp = 0.1;
            $this->PSiO2p = 1;
            $this->PSnp = 0.1;
            $this->AgUSD = 75;
            $this->tc = 8.70;
            $this->flete = 160;
            $this->rollback = 42;
            $this->comibol = 1;
            $this->NIM = 1.55;
            $this->zinc = 54.06;
            $this->Zn = 3434;
            $this->regaliaZn = 3;
        }
    }

    private function setAlpineProperties(Liquidation $liquidation): void
    {
        $this->NIM = $liquidation->quincenal_zn;
        $this->nimPb = $liquidation->quincenal_pb;
        $this->Zn = $liquidation->market_zn;
        $this->Pb = $liquidation->market_pb;
        $this->agNIM = $liquidation->quincenal_ag;
        $this->AgUSD = $liquidation->market_ag;
        $this->tmh = $liquidation->tmh;
        $this->h2o = $liquidation->h2o;
        $this->merma = $liquidation->merma;
        $this->dm = $liquidation->dm;
        $this->zinc = $liquidation->zinc_grade;
        $this->plomo = $liquidation->lead_grade;
        $this->maquila = $liquidation->maquila;
        $this->base = $liquidation->base;
        $this->As = $liquidation->as_pct;
        $this->Sb = $liquidation->sb_pct;
        $this->Fe = $liquidation->fe_pct;
        $this->SiO2 = $liquidation->sio2_pct;
        $this->Sn = $liquidation->sn_pct;
        $this->PAs = $liquidation->p_as;
        $this->PAsUSD = $liquidation->p_as_usd;
        $this->PAsp = $liquidation->p_as_pct;
        $this->PSb = $liquidation->p_sb;
        $this->PSbUSD = $liquidation->p_sb_usd;
        $this->PSbp = $liquidation->p_sb_pct;
        $this->PFe = $liquidation->p_fe;
        $this->PFeUSD = $liquidation->p_fe_usd;
        $this->PFep = $liquidation->p_fe_pct;
        $this->PSiO2 = $liquidation->p_sio2;
        $this->PSiO2USD = $liquidation->p_sio2_usd;
        $this->PSiO2p = $liquidation->p_sio2_pct;
        $this->PSn = $liquidation->p_sn;
        $this->PSnUSD = $liquidation->p_sn_usd;
        $this->PSnp = $liquidation->p_sn_pct;
        $this->basePorcentaje = $liquidation->base_percentage;
        $this->refinacion = $liquidation->refinacion;
        $this->flete = $liquidation->flete;
        $this->rollback = $liquidation->rollback;
        $this->remesaPct = $liquidation->remesa_pct;
        $this->tc = $liquidation->tc;
        $this->regaliaZn = $liquidation->regalia_zn;
        $this->regaliaPb = $liquidation->regalia_pb;
        $this->regaliaAg = $liquidation->regalia_ag;
        $this->factorRegalia = $liquidation->factor_regalia;
        $this->cns = $liquidation->cns_pct;
        $this->comibol = $liquidation->comibol_pct;
        $this->fedecomin = $liquidation->fedecomin_pct;
        $this->fencomin = $liquidation->fencomin_pct;
        $this->aporteCoop = $liquidation->aporte_coop_pct;
    }

    public function render()
    {
        return view('livewire.liquidations.liquidation-form');
    }

    protected function getListeners()
    {
        return [
            'supplier-selected' => 'onSupplierSelected',
            'supplier-ci-manual' => 'onSupplierCiManual',
        ];
    }

    public function onSupplierCiManual($payload)
    {
        $this->ci = $payload['ci'];
    }

    public function onSupplierSelected($payload)
    {
        $this->ci = $payload['ci'];
        $this->full_name = $payload['full_name'];
        $this->customer_id = $payload['id'];
        $this->nim = $payload['NIM'];
        $this->nit = $payload['NIT'];
        $this->concession = $payload['concession'];
        $this->mine = $payload['mine'];
        $this->municipality = $payload['municipality'];
        $this->cooperative_name = $payload['name'];
        $this->aporteCoop = $payload['contribution'] ?? 0;
        $this->dispatch('aporte-coop-sync', value: (float) ($payload['contribution'] ?? 0));
    }

    public function save()
    {
        if ($this->id) {
            $this->update();
        } else {
            $this->store();
        }
    }

    public function store(array $alpineData = [])
    {
        $this->applyAlpineData($alpineData);
        $this->authorize('Crear contratos');
        $this->validate();

        DB::transaction(function () {
            Liquidation::create($this->collectData());
        });

        $this->toast()
            ->success('Liquidación guardada', 'La liquidación fue registrada correctamente')
            ->send();

        return redirect()->route('liquidations');
    }

    public function update(array $alpineData = [])
    {
        $this->applyAlpineData($alpineData);
        $this->authorize('Crear contratos');
        $this->validate();

        DB::transaction(function () {
            Liquidation::findOrFail($this->id)->update($this->collectData());
        });

        $this->toast()
            ->success('Liquidación actualizada', 'La liquidación fue actualizada correctamente')
            ->send();

        return redirect()->route('liquidations');
    }

    private function applyAlpineData(array $data): void
    {
        $props = [
            'metal', 'tmh', 'h2o', 'merma', 'dm', 'base', 'basePorcentaje', 'maquila',
            'Sb', 'Fe', 'SiO2', 'Sn', 'As',
            'PAs', 'PAsUSD', 'PAsp', 'PFe', 'PFeUSD', 'PFep',
            'PSb', 'PSbUSD', 'PSbp', 'PSiO2', 'PSiO2USD', 'PSiO2p',
            'PSn', 'PSnUSD', 'PSnp',
            'AgUSD', 'agNIM', 'regaliaAg', 'factorRegalia',
            'tc', 'flete', 'rollback', 'remesaPct',
            'cns', 'comibol', 'fedecomin', 'fencomin', 'aporteCoop',
            'NIM', 'zinc', 'Zn', 'regaliaZn',
            'nimPb', 'plomo', 'Pb', 'regaliaPb', 'refinacion',
        ];
        foreach ($props as $p) {
            if (array_key_exists($p, $data)) {
                $this->{$p} = $data[$p];
            }
        }
    }

    private function collectData(): array
    {
        return [
            'metal' => $this->metal,
            'lote' => $this->lote,
            'date' => $this->date,
            'customer_id' => $this->customer_id,
            'full_name' => $this->full_name,
            'nim' => $this->nim,
            'nit' => $this->nit,
            'concession' => $this->concession,
            'mine' => $this->mine,
            'municipality' => $this->municipality,
            'cooperative_name' => $this->cooperative_name,
            'lab_quimico' => $this->lab_quimico,
            'number_lab' => $this->number_lab,
            'codigo' => $this->codigo,
            'quincenal_zn' => $this->cv($this->NIM),
            'quincenal_pb' => $this->cv($this->nimPb),
            'market_zn' => $this->cv($this->Zn),
            'market_pb' => $this->cv($this->Pb),
            'quincenal_ag' => $this->cv($this->agNIM),
            'market_ag' => $this->cv($this->AgUSD),
            'tmh' => $this->cv($this->tmh),
            'h2o' => $this->cv($this->h2o),
            'merma' => $this->cv($this->merma),
            'dm' => $this->cv($this->dm),
            'zinc_grade' => $this->cv($this->zinc),
            'lead_grade' => $this->cv($this->plomo),
            'maquila' => $this->cv($this->maquila),
            'base' => $this->cv($this->base),
            'as_pct' => $this->cv($this->As),
            'sb_pct' => $this->cv($this->Sb),
            'fe_pct' => $this->cv($this->Fe),
            'sio2_pct' => $this->cv($this->SiO2),
            'sn_pct' => $this->cv($this->Sn),
            'p_as' => $this->cv($this->PAs),
            'p_as_usd' => $this->cv($this->PAsUSD),
            'p_as_pct' => $this->cv($this->PAsp),
            'p_sb' => $this->cv($this->PSb),
            'p_sb_usd' => $this->cv($this->PSbUSD),
            'p_sb_pct' => $this->cv($this->PSbp),
            'p_fe' => $this->cv($this->PFe),
            'p_fe_usd' => $this->cv($this->PFeUSD),
            'p_fe_pct' => $this->cv($this->PFep),
            'p_sio2' => $this->cv($this->PSiO2),
            'p_sio2_usd' => $this->cv($this->PSiO2USD),
            'p_sio2_pct' => $this->cv($this->PSiO2p),
            'p_sn' => $this->cv($this->PSn),
            'p_sn_usd' => $this->cv($this->PSnUSD),
            'p_sn_pct' => $this->cv($this->PSnp),
            'base_percentage' => $this->cv($this->basePorcentaje),
            'refinacion' => $this->cv($this->refinacion),
            'flete' => $this->cv($this->flete),
            'rollback' => $this->cv($this->rollback),
            'remesa_pct' => $this->cv($this->remesaPct),
            'tc' => $this->cv($this->tc),
            'regalia_zn' => $this->cv($this->regaliaZn),
            'regalia_pb' => $this->cv($this->regaliaPb),
            'regalia_ag' => $this->cv($this->regaliaAg),
            'factor_regalia' => $this->cv($this->factorRegalia),
            'cns_pct' => $this->cv($this->cns),
            'comibol_pct' => $this->cv($this->comibol),
            'fedecomin_pct' => $this->cv($this->fedecomin),
            'fencomin_pct' => $this->cv($this->fencomin),
            'aporte_coop_pct' => $this->cv($this->aporteCoop),
            'user_id' => auth()->id(),
        ];
    }

    private function cv($value): float
    {
        return is_numeric($value) ? (float) $value : 0;
    }

    public function rules()
    {
        $rules = [
            'metal' => 'required|in:zn,pb',
            'lote' => 'required|string|max:50',
            'date' => 'required|date',
            'customer_id' => 'nullable|exists:customers,id',
            'full_name' => 'required|string|max:255',
            'tmh' => 'required|numeric|min:0.001',
        ];

        if ($this->metal === 'pb') {
            $rules['plomo'] = 'required|numeric|min:0.001';
            $rules['Pb'] = 'required|numeric|min:0.001';
            $rules['nimPb'] = 'required|numeric|min:0';
        } else {
            $rules['zinc'] = 'required|numeric|min:0.001';
            $rules['Zn'] = 'required|numeric|min:0.001';
            $rules['NIM'] = 'required|numeric|min:0';
        }

        return $rules;
    }

    public function clear()
    {
        $this->reset();
        $this->resetValidation();
        $this->date = now()->format('Y-m-d');
        $this->metal = 'zn';
        $this->dispatch('close-modal');
    }
}
