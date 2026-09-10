<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['guard_name' => 'web', 'name' => 'Ver dashboard', 'description' => 'Puede ver el panel principal del sistema'],
            ['guard_name' => 'web', 'name' => 'Crear usuarios', 'description' => 'Puede crear nuevos usuarios'],
            ['guard_name' => 'web', 'name' => 'Editar usuarios', 'description' => 'Puede editar la información de los usuarios'],
            ['guard_name' => 'web', 'name' => 'Eliminar usuarios', 'description' => 'Puede eliminar registros de usuarios'],
            ['guard_name' => 'web', 'name' => 'Ver usuarios', 'description' => 'Puede ver la lista y los detalles de usuarios'],

            ['guard_name' => 'web', 'name' => 'Ver roles', 'description' => 'Puede ver la lista y los detalles de roles'],
            ['guard_name' => 'web', 'name' => 'Editar roles', 'description' => 'Puede editar los datos de los roles'],
            ['guard_name' => 'web', 'name' => 'Eliminar roles', 'description' => 'Puede Eliminar roles existentes'],
            ['guard_name' => 'web', 'name' => 'Crear roles', 'description' => 'Puede crear nuevos roles'],
            ['guard_name' => 'web', 'name' => 'Asignación de permisos', 'description' => 'Puede controlar los permisos de cada tipo de usuarios'],

            ['guard_name' => 'web', 'name' => 'Crear retenciones', 'description' => 'Puede crear nuevos retenciones'],
            ['guard_name' => 'web', 'name' => 'Editar retenciones', 'description' => 'Puede editar la información de los retenciones existentes'],
            ['guard_name' => 'web', 'name' => 'Eliminar retenciones', 'description' => 'Puede eliminar retenciones existentes'],
            ['guard_name' => 'web', 'name' => 'Ver retenciones', 'description' => 'Puede ver la lista y los detalles de retenciones'],
            ['guard_name' => 'web', 'name' => 'Exportar retenciones', 'description' => 'Puede exportar las retenciones de una determinada fecha en formato Excel'],

            ['guard_name' => 'web', 'name' => 'Crear impuestos', 'description' => 'Puede crear nuevos impuestos'],
            ['guard_name' => 'web', 'name' => 'Editar impuestos', 'description' => 'Puede editar la información de los impuestos'],
            ['guard_name' => 'web', 'name' => 'Eliminar impuestos', 'description' => 'Puede eliminar registros de impuestos'],
            ['guard_name' => 'web', 'name' => 'Ver impuestos', 'description' => 'Puede ver la lista y los detalles de impuestos'],

            ['guard_name' => 'web', 'name' => 'Crear cuentas', 'description' => 'Puede crear nuevas cuentas'],
            ['guard_name' => 'web', 'name' => 'Editar cuentas', 'description' => 'Puede editar la información de las cuentas'],
            ['guard_name' => 'web', 'name' => 'Eliminar cuentas', 'description' => 'Puede eliminar registros de cuentas'],
            ['guard_name' => 'web', 'name' => 'Ver cuentas', 'description' => 'Puede ver la lista y los detalles de las  cuentas'],

            ['guard_name' => 'web', 'name' => 'Ver libro de bancos', 'description' => 'Puede ver la lista y los detalles del libro de bancos'],
            ['guard_name' => 'web', 'name' => 'PDF libro de bancos', 'description' => 'Puede exportar el libro de bancos en formato PDF'],
            ['guard_name' => 'web', 'name' => 'Excel libro de bancos', 'description' => 'Puede expotar el libro de bancos en formato Excel'],

            ['guard_name' => 'web', 'name' => 'Crear movimientos de libro de bancos', 'description' => 'Puede crear nuevos movimientos de libro de bancos'],
            ['guard_name' => 'web', 'name' => 'Editar movimientos de libro de bancos', 'description' => 'Puede editar la información de un movimiento del libro de bancos'],
            ['guard_name' => 'web', 'name' => 'Eliminar movimientos de libro de bancos', 'description' => 'Puede eliminar movimientos de libro de bancos'],
            ['guard_name' => 'web', 'name' => 'Ver movimiento de libro de bancos', 'description' => 'Puede ver el detalle un movimiento en formato PDF'],

            ['guard_name' => 'web', 'name' => 'Ver estados de cuenta', 'description' => 'Puede ver la lista y el detalle de estados de cuenta de clientes y proveedores'],
            ['guard_name' => 'web', 'name' => 'Crear estados de cuenta', 'description' => 'Puede registrar clientes, proveedores y movimientos en estados de cuenta'],
            ['guard_name' => 'web', 'name' => 'Editar estados de cuenta', 'description' => 'Puede editar clientes, proveedores y movimientos de estados de cuenta'],
            ['guard_name' => 'web', 'name' => 'Eliminar estados de cuenta', 'description' => 'Puede eliminar clientes, proveedores y movimientos de estados de cuenta'],
            ['guard_name' => 'web', 'name' => 'PDF estados de cuenta', 'description' => 'Puede exportar estados de cuenta en formato PDF'],

            /* ── Caja chica ── */
            ['guard_name' => 'web', 'name' => 'Ver caja chica', 'description' => 'Puede ver el libro de caja chica'],
            ['guard_name' => 'web', 'name' => 'Crear caja chica', 'description' => 'Puede crear movimientos de caja chica'],
            ['guard_name' => 'web', 'name' => 'Editar caja chica', 'description' => 'Puede editar movimientos de caja chica'],
            ['guard_name' => 'web', 'name' => 'Eliminar caja chica', 'description' => 'Puede eliminar movimientos de caja chica'],
            ['guard_name' => 'web', 'name' => 'PDF caja chica', 'description' => 'Puede exportar el libro de caja chica en formato PDF'],
            ['guard_name' => 'web', 'name' => 'Excel caja chica', 'description' => 'Puede exportar el libro de caja chica en formato Excel'],

            /* ── Proveedores ── */
            ['guard_name' => 'web', 'name' => 'Ver proveedores', 'description' => 'Puede ver la lista y los detalles de proveedores'],
            ['guard_name' => 'web', 'name' => 'Crear proveedores', 'description' => 'Puede crear nuevos proveedores'],
            ['guard_name' => 'web', 'name' => 'Editar proveedores', 'description' => 'Puede editar la información de los proveedores'],
            ['guard_name' => 'web', 'name' => 'Eliminar proveedores', 'description' => 'Puede eliminar proveedores existentes'],

            /* ── Clientes ── */
            ['guard_name' => 'web', 'name' => 'Ver clientes', 'description' => 'Puede ver la lista y los detalles de clientes'],
            ['guard_name' => 'web', 'name' => 'Crear clientes', 'description' => 'Puede crear nuevos clientes'],
            ['guard_name' => 'web', 'name' => 'Editar clientes', 'description' => 'Puede editar la información de los clientes'],
            ['guard_name' => 'web', 'name' => 'Eliminar clientes', 'description' => 'Puede eliminar clientes existentes'],

            /* ── Departamentos ── */
            ['guard_name' => 'web', 'name' => 'Ver departamentos', 'description' => 'Puede ver la lista y los detalles de departamentos'],
            ['guard_name' => 'web', 'name' => 'Crear departamentos', 'description' => 'Puede crear nuevos departamentos'],
            ['guard_name' => 'web', 'name' => 'Editar departamentos', 'description' => 'Puede editar la información de los departamentos'],
            ['guard_name' => 'web', 'name' => 'Eliminar departamentos', 'description' => 'Puede eliminar departamentos existentes'],

            /* ── Cooperativas ── */
            ['guard_name' => 'web', 'name' => 'Ver cooperativas', 'description' => 'Puede ver la lista y los detalles de cooperativas'],
            ['guard_name' => 'web', 'name' => 'Crear cooperativas', 'description' => 'Puede crear nuevas cooperativas'],
            ['guard_name' => 'web', 'name' => 'Editar cooperativas', 'description' => 'Puede editar la información de las cooperativas'],
            ['guard_name' => 'web', 'name' => 'Eliminar cooperativas', 'description' => 'Puede eliminar cooperativas existentes'],

            /* ── Reportes ── */
            ['guard_name' => 'web', 'name' => 'Ver reportes', 'description' => 'Puede ver todos los reportes del sistema'],
            ['guard_name' => 'web', 'name' => 'Exportar reportes', 'description' => 'Puede exportar reportes en formato PDF y Excel'],

            /* ── Liquidaciones ── */
            ['guard_name' => 'web', 'name' => 'Ver liquidaciones', 'description' => 'Puede ver el formulario de liquidaciones'],

            /* ── Cotizaciones ── */
            ['guard_name' => 'web', 'name' => 'Ver cotizaciones', 'description' => 'Puede ver las cotizaciones de minerales'],

            /* ── Contratos ── */
            ['guard_name' => 'web', 'name' => 'Ver contratos', 'description' => 'Puede ver la lista y detalle de contratos'],
            ['guard_name' => 'web', 'name' => 'Crear contratos', 'description' => 'Puede crear nuevos contratos'],
            ['guard_name' => 'web', 'name' => 'Editar contratos', 'description' => 'Puede editar contratos existentes'],
            ['guard_name' => 'web', 'name' => 'Eliminar contratos', 'description' => 'Puede eliminar contratos'],
            ['guard_name' => 'web', 'name' => 'PDF contratos', 'description' => 'Puede exportar contratos en formato PDF'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name'], 'guard_name' => $permission['guard_name']],
                ['description' => $permission['description']]
            );
        }

        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->syncPermissions(Permission::pluck('name'));
        }
    }
}
