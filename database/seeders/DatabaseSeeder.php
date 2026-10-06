<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Departament;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::create([
            'name' => 'Hamura',
            'last_name' => 'Otsutsuki',
            'birthdate' => '2001-06-18',
            'gender' => 'M',
            'ci' => '123023',
            'phone' => '77710293',
            'email' => 'deidaramen2@gmail.com',
            'password' => Hash::make('madaradelos6'),
        ]);
        User::create([
            'name' => 'Jimena',
            'last_name' => 'Torrez',
            'birthdate' => '2001-06-18',
            'gender' => 'F',
            'ci' => '1342331',
            'phone' => '73321223',
            'email' => 'jimena12@gmail.com',
            'password' => Hash::make('jimena'),
        ]);
        User::create([
            'name' => 'Yoselin',
            'last_name' => 'Cahuana',
            'birthdate' => '2000-06-18',
            'gender' => 'F',
            'ci' => '15431233',
            'phone' => '71321223',
            'email' => 'yoselin@gmail.com',
            'password' => Hash::make('yoselin'),
        ]);
        User::create([
            'name' => 'Iver',
            'last_name' => 'Villanueva',
            'birthdate' => '2001-06-18',
            'gender' => 'M',
            'ci' => '10332315',
            'phone' => '75321223',
            'email' => 'iver@gmail.com',
            'password' => Hash::make('iver'),
        ]);
        Account::create([
            'name' => 'BANCO NACIONAL DE BOLIVIA',
            'account_number' => '600-0050774',
            'initials' => 'BNB.SA',
            'currency_type' => 'BOB',
        ]);
        Account::create([
            'name' => 'ANCHOR BANK',
            'account_number' => '20011771',
            'initials' => '',
            'currency_type' => 'usd',
        ]);

        Account::create([
            'name' => 'BANCO DE CREDITO DE BOLIVIA',
            'account_number' => '301-5096725-3-02',
            'initials' => 'BCP.SA',
            'currency_type' => 'BOB',
        ]);
        Account::create([
            'name' => 'BANCO GANADERO S.A',
            'account_number' => '1311817580',
            'initials' => 'BGA',
            'currency_type' => 'BOB',
        ]);
        Account::create([
            'name' => 'BANCO FORTALEZA S.A',
            'account_number' => '9241000049',
            'initials' => 'BFO.SA',
            'currency_type' => 'BOB',
        ]);

        Account::create([
            'name' => 'BANCO UNION S.A',
            'account_number' => '1-54532764',
            'initials' => 'BUN.SA',
            'currency_type' => 'BOB',
        ]);

        Account::create([
            'name' => 'BANCO BISA',
            'account_number' => '946080012',
            'initials' => 'BISA.SA',
            'currency_type' => 'BOB',
        ]);

        Account::create([
            'name' => 'BANCO UNION S.A',
            'account_number' => '10000063431478',
            'initials' => 'BUN.SA',
            'currency_type' => 'BOB',
        ]);

        Account::create([
            'name' => 'BANCO NACIONAL DE BOLIVIA',
            'account_number' => '600-0051436',
            'initials' => 'BNB.SA',
            'currency_type' => 'BOB',
        ]);

        $rrhhAccount = Account::create([
            'name' => 'BANCO NACIONAL DE BOLIVIA',
            'account_number' => '600-0050774',
            'initials' => 'BNB.SA',
            'currency_type' => 'BOB',
        ]);

        Departament::create([
            'area' => 'Recursos Humanos',
            'description' => 'Departamento de Recursos Humanos',
            'account_id' => $rrhhAccount->id,
            'user_id' => $user->id,
        ]);

        $this->call(CooperativeSeeder::class);
        $this->call(CustomerSeeder::class);
        $this->call(TaxeSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(InventoryRrhhPermissionSeeder::class);
        $permissionsAll = Permission::pluck('id')->toArray();
        $rol = Role::Create([
            'name' => 'Admin',

        ]);
        $rol->syncPermissions($permissionsAll);
        $user->syncRoles([$rol->id]);

    /*     $this->call(DemoDataSeeder::class);
        $this->call(MoreDataSeeder::class);
        $this->call(TodayDataSeeder::class); */
    }
}
