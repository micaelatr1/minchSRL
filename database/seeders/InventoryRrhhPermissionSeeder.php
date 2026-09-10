<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InventoryRrhhPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            /* ── Productos ── */
            ['guard_name' => 'web', 'name' => 'Ver productos', 'description' => 'Puede ver la lista de productos'],
            ['guard_name' => 'web', 'name' => 'Crear productos', 'description' => 'Puede crear nuevos productos'],
            ['guard_name' => 'web', 'name' => 'Editar productos', 'description' => 'Puede editar productos existentes'],
            ['guard_name' => 'web', 'name' => 'Eliminar productos', 'description' => 'Puede eliminar productos'],

            /* ── Kardex ── */
            ['guard_name' => 'web', 'name' => 'Ver kardex', 'description' => 'Puede ver el kardex de productos'],
            ['guard_name' => 'web', 'name' => 'PDF kardex', 'description' => 'Puede exportar el kardex en formato PDF'],

            /* ── Entradas inventario (compras) ── */
            ['guard_name' => 'web', 'name' => 'Ver entradas inventario', 'description' => 'Puede ver los ingresos por compra'],
            ['guard_name' => 'web', 'name' => 'Crear entradas inventario', 'description' => 'Puede registrar ingresos por compra'],
            ['guard_name' => 'web', 'name' => 'Editar entradas inventario', 'description' => 'Puede editar ingresos por compra'],
            ['guard_name' => 'web', 'name' => 'Eliminar entradas inventario', 'description' => 'Puede eliminar ingresos por compra'],

            /* ── Consumos inventario ── */
            ['guard_name' => 'web', 'name' => 'Ver consumos inventario', 'description' => 'Puede ver los consumos de inventario'],
            ['guard_name' => 'web', 'name' => 'Crear consumos inventario', 'description' => 'Puede registrar consumos de inventario'],

            /* ── Ajustes inventario ── */
            ['guard_name' => 'web', 'name' => 'Ver ajustes inventario', 'description' => 'Puede ver los ajustes de inventario'],
            ['guard_name' => 'web', 'name' => 'Crear ajustes inventario', 'description' => 'Puede registrar ajustes de inventario'],

            /* ── Banco inventario ── */
            ['guard_name' => 'web', 'name' => 'Ver banco inventario', 'description' => 'Puede ver la cuenta bancaria asignada'],
            ['guard_name' => 'web', 'name' => 'PDF banco inventario', 'description' => 'Puede exportar movimientos del banco a PDF'],

            /* ── RRHH: Empleados ── */
            ['guard_name' => 'web', 'name' => 'Ver empleados', 'description' => 'Puede ver la lista de empleados'],
            ['guard_name' => 'web', 'name' => 'Crear empleados', 'description' => 'Puede crear nuevos empleados'],
            ['guard_name' => 'web', 'name' => 'Editar empleados', 'description' => 'Puede editar empleados existentes'],
            ['guard_name' => 'web', 'name' => 'Eliminar empleados', 'description' => 'Puede eliminar empleados'],

            /* ── RRHH: Planillas ── */
            ['guard_name' => 'web', 'name' => 'Ver planillas', 'description' => 'Puede ver la lista de planillas de sueldos'],
            ['guard_name' => 'web', 'name' => 'Crear planillas', 'description' => 'Puede crear nuevas planillas'],
            ['guard_name' => 'web', 'name' => 'Editar planillas', 'description' => 'Puede editar planillas existentes'],
            ['guard_name' => 'web', 'name' => 'Pagar planillas', 'description' => 'Puede marcar planillas como pagadas'],
            ['guard_name' => 'web', 'name' => 'Eliminar planillas', 'description' => 'Puede eliminar planillas'],

            /* ── RRHH: Vacaciones ── */
            ['guard_name' => 'web', 'name' => 'Ver vacaciones', 'description' => 'Puede ver las vacaciones de empleados'],
            ['guard_name' => 'web', 'name' => 'Crear vacaciones', 'description' => 'Puede registrar vacaciones'],
            ['guard_name' => 'web', 'name' => 'Editar vacaciones', 'description' => 'Puede editar vacaciones existentes'],
            ['guard_name' => 'web', 'name' => 'Eliminar vacaciones', 'description' => 'Puede eliminar vacaciones'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name'], 'guard_name' => $permission['guard_name']],
                ['description' => $permission['description']]
            );
        }

        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo(Permission::whereIn('name', array_column($permissions, 'name'))->pluck('name'));
        }
    }
}
