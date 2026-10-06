<?php

namespace Database\Factories;

use App\Models\Liquidation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LiquidationFactory extends Factory
{
    protected $model = Liquidation::class;

    public function definition(): array
    {
        return array_merge($this->commonAttributes(), $this->znAttributes());
    }

    public function zn(): static
    {
        return $this->state($this->znAttributes());
    }

    public function pb(): static
    {
        return $this->state($this->pbAttributes());
    }

    private function commonAttributes(): array
    {
        return [
            'customer_id' => null,
            'full_name' => fake()->name(),
            'nim' => fake()->numerify('NIM-####'),
            'nit' => fake()->numerify('####'),
            'concession' => fake()->sentence(2),
            'mine' => fake()->company(),
            'municipality' => fake()->city(),
            'cooperative_name' => 'COOPERATIVA '.strtoupper(fake()->companySuffix()),
            'lab_quimico' => 'LABMIN',
            'number_lab' => fake()->numerify('LAB-####'),
            'codigo' => fake()->numerify('C-####'),

            'quincenal_ag' => 76.38,
            'market_ag' => 75,

            'merma' => 1,

            'p_as' => 0.5,
            'p_sb' => 0.5,
            'p_fe' => 8,
            'p_sio2' => 3,
            'p_sn' => 0.5,
            'p_as_pct' => 0.1,
            'p_sb_pct' => 0.1,
            'p_fe_pct' => 1,
            'p_sio2_pct' => 1,
            'p_sn_pct' => 0.1,

            'base_percentage' => 0.15,
            'refinacion' => 0.05,

            'regalia_ag' => 3.6,
            'factor_regalia' => 6.96,

            'cns_pct' => 1.8,
            'fedecomin_pct' => 1,
            'fencomin_pct' => 0.4,
            'aporte_coop_pct' => 0,

            'date' => fake()->dateTimeBetween('-3 months', 'now'),
            'user_id' => User::factory(),
        ];
    }

    private function znAttributes(): array
    {
        return [
            'metal' => 'zn',
            'lote' => 'MCH-ZN-'.fake()->unique()->numberBetween(1, 99).'/26',

            'quincenal_zn' => 1.55,
            'quincenal_pb' => 0,
            'market_zn' => 3434,
            'market_pb' => 0,

            'tmh' => 22.720,
            'h2o' => 11.45,
            'dm' => 6.91,
            'zinc_grade' => 54.06,
            'lead_grade' => 0,
            'maquila' => 90,
            'base' => 3000,

            'as_pct' => 0.1,
            'sb_pct' => 0.1,
            'fe_pct' => 9.5,
            'sio2_pct' => 5,
            'sn_pct' => 0,
            'p_as_usd' => 3,
            'p_sb_usd' => 3,
            'p_fe_usd' => 3,
            'p_sio2_usd' => 3,
            'p_sn_usd' => 3,

            'flete' => 160,
            'rollback' => 42,
            'remesa_pct' => 0.8,
            'remesa_fijo' => 385,
            'tc' => 8.70,

            'regalia_zn' => 3,
            'regalia_pb' => 0,
            'comibol_pct' => 1,
        ];
    }

    private function pbAttributes(): array
    {
        return [
            'metal' => 'pb',
            'lote' => 'MCPB-'.fake()->unique()->numberBetween(1, 99).'/26',

            'quincenal_zn' => 0,
            'quincenal_pb' => 0.88,
            'market_zn' => 0,
            'market_pb' => 1936,

            'tmh' => 35.920,
            'h2o' => 10.66815,
            'dm' => 24.21,
            'zinc_grade' => 0,
            'lead_grade' => 52.88,
            'maquila' => 0,
            'base' => 2000,

            'as_pct' => 1.30,
            'sb_pct' => 1.30,
            'fe_pct' => 6.20,
            'sio2_pct' => 3,
            'sn_pct' => 0,
            'p_as_usd' => 3.5,
            'p_sb_usd' => 3.5,
            'p_fe_usd' => 3.5,
            'p_sio2_usd' => 3.5,
            'p_sn_usd' => 3.5,

            'flete' => 180,
            'rollback' => 38,
            'remesa_pct' => 0.6,
            'remesa_fijo' => 305,
            'tc' => 9.10,

            'regalia_zn' => 0,
            'regalia_pb' => 3,
            'comibol_pct' => 0,
        ];
    }
}
