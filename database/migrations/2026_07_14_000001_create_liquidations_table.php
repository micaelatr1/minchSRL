<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('liquidations', function (Blueprint $table) {
            $table->id();

            // Metal type
            $table->string('metal', 2); // 'zn' or 'pb'

            // General data (auto-filled from cooperative search)
            $table->string('lote');
            $table->date('date');
            $table->foreignId('customer_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('nim')->nullable();
            $table->string('nit')->nullable();
            $table->string('concession')->nullable();
            $table->string('mine')->nullable();
            $table->string('municipality')->nullable();
            $table->string('cooperative_name')->nullable();
            $table->string('lab_quimico')->nullable();
            $table->string('number_lab')->nullable();
            $table->string('codigo')->nullable();

            // Metal quotations
            $table->decimal('quincenal_zn', 12, 2)->default(0);
            $table->decimal('quincenal_pb', 12, 2)->default(0);
            $table->decimal('market_zn', 12, 2)->default(0);
            $table->decimal('market_pb', 12, 2)->default(0);
            $table->decimal('quincenal_ag', 12, 2)->default(0);
            $table->decimal('market_ag', 12, 2)->default(0);

            // Weights and grades
            $table->decimal('tmh', 12, 3)->default(0);
            $table->decimal('h2o', 12, 5)->default(0);
            $table->decimal('merma', 12, 2)->default(0);
            $table->decimal('dm', 12, 2)->default(0);
            $table->decimal('zinc_grade', 12, 2)->default(0);
            $table->decimal('lead_grade', 12, 2)->default(0);
            $table->decimal('maquila', 12, 2)->default(0);
            $table->decimal('base', 12, 2)->default(0);

            // Penalty percentages (contaminants)
            $table->decimal('as_pct', 12, 2)->default(0);
            $table->decimal('sb_pct', 12, 2)->default(0);
            $table->decimal('fe_pct', 12, 2)->default(0);
            $table->decimal('sio2_pct', 12, 2)->default(0);
            $table->decimal('sn_pct', 12, 2)->default(0);

            // Penalty thresholds for each contaminant
            $table->decimal('p_as', 12, 2)->default(0);
            $table->decimal('p_as_usd', 12, 2)->default(0);
            $table->decimal('p_as_pct', 12, 2)->default(0);
            $table->decimal('p_sb', 12, 2)->default(0);
            $table->decimal('p_sb_usd', 12, 2)->default(0);
            $table->decimal('p_sb_pct', 12, 2)->default(0);
            $table->decimal('p_fe', 12, 2)->default(0);
            $table->decimal('p_fe_usd', 12, 2)->default(0);
            $table->decimal('p_fe_pct', 12, 2)->default(0);
            $table->decimal('p_sio2', 12, 2)->default(0);
            $table->decimal('p_sio2_usd', 12, 2)->default(0);
            $table->decimal('p_sio2_pct', 12, 2)->default(0);
            $table->decimal('p_sn', 12, 2)->default(0);
            $table->decimal('p_sn_usd', 12, 2)->default(0);
            $table->decimal('p_sn_pct', 12, 2)->default(0);

            // Treatment parameters
            $table->decimal('base_percentage', 12, 2)->default(0);
            $table->decimal('refinacion', 12, 2)->default(0);

            // Expenses
            $table->decimal('flete', 12, 2)->default(0);
            $table->decimal('rollback', 12, 2)->default(0);
            $table->decimal('remesa_pct', 12, 2)->default(0);
            $table->decimal('tc', 12, 2)->default(0);

            // Royalties
            $table->decimal('regalia_zn', 12, 2)->default(0);
            $table->decimal('regalia_pb', 12, 2)->default(0);
            $table->decimal('regalia_ag', 12, 2)->default(0);
            $table->decimal('factor_regalia', 12, 2)->default(0);

            // Contributions
            $table->decimal('cns_pct', 12, 2)->default(0);
            $table->decimal('comibol_pct', 12, 2)->default(0);
            $table->decimal('fedecomin_pct', 12, 2)->default(0);
            $table->decimal('fencomin_pct', 12, 2)->default(0);
            $table->decimal('aporte_coop_pct', 12, 2)->default(0);

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('liquidations');
    }
};
