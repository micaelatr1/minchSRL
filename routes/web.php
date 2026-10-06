<?php

use App\Http\Controllers\ExcelController;
use App\Http\Controllers\PdfController;
use App\Livewire\AccountBoxes\AccountBoxComponent;
use App\Livewire\AccountBoxes\BoxFormComponent;
use App\Livewire\Accounts\AccountComponent;
use App\Livewire\AccountStatements\AccountStatementComponent;
use App\Livewire\AccountStatements\AccountStatementView;
use App\Livewire\Contracts\ContractComponent;
use App\Livewire\Contracts\ContractFormComponent;
use App\Livewire\Contracts\ContractShowComponent;
use App\Livewire\Cooperatives\CooperativeComponent;
use App\Livewire\Cotizaciones\CotizacionComponent;
use App\Livewire\Customers\CustomerComponent;
use App\Livewire\Dashboard;
use App\Livewire\Departaments\DepartamentComponent;
use App\Livewire\Inventory\BankComponent;
use App\Livewire\Inventory\KardexComponent;
use App\Livewire\Inventory\ProductComponent;
use App\Livewire\Inventory\ProductFormComponent;
use App\Livewire\Inventory\PurchaseComponent;
use App\Livewire\Inventory\PurchaseFormComponent;
use App\Livewire\Liquidations\LiquidationComponent;
use App\Livewire\Liquidations\LiquidationForm;
use App\Livewire\Permissions\PermissionComponent;
use App\Livewire\Reports\BankBookReportsComponent;
use App\Livewire\Reports\BoxReportsComponent;
use App\Livewire\Reports\LiquidationReportsComponent;
use App\Livewire\Reports\RetentionReportsComponent;
use App\Livewire\Retentions\RetentionComponent;
use App\Livewire\Retentions\RetentionComponentForm;
use App\Livewire\Roles\RoleComponent;
use App\Livewire\Suppliers\SupplierComponent;
use App\Livewire\Taxe\TaxeComponent;
use App\Livewire\Transactions\TransactionComponent;
use App\Livewire\Transactions\TransactionFormComponent;
use App\Livewire\Transactions\ViewComponent;
use App\Livewire\Users\UserComponent;
use App\Services\AccountStatementService;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('permission:Ver dashboard')->group(function () {
        Route::get('dashboard', Dashboard::class)->name('dashboard');
    });

    Route::middleware('permission:Ver impuestos')->group(function () {
        Route::get('taxes', TaxeComponent::class)->name('taxes');
    });

    Route::middleware('permission:Ver cuentas')->group(function () {
        Route::get('accounts', AccountComponent::class)->name('accounts');
    });

    Route::middleware('permission:Ver estados de cuenta')->group(function () {
        Route::get('accounts-statement', AccountStatementComponent::class)->name('accounts.statement');
        Route::get('accounts-statement/{type}/{id}', AccountStatementView::class)
            ->whereIn('type', AccountStatementService::TYPES)
            ->name('accounts.statement.view');
    });

    Route::middleware('permission:PDF estados de cuenta')->group(function () {
        Route::get('account/statement/pdf/{type}/{id}', [PdfController::class, 'accountStatement'])
            ->whereIn('type', AccountStatementService::TYPES)
            ->name('account.statement.pdf');
    });

    Route::middleware('permission:Crear contratos|Editar contratos')->group(function () {
        Route::get('contracts/form/{id?}', ContractFormComponent::class)->name('contracts.form');
    });

    Route::middleware('permission:Ver contratos')->group(function () {
        Route::get('contracts', ContractComponent::class)->name('contracts');
        Route::get('contracts/{contract}', ContractShowComponent::class)->name('contracts.show');
        Route::get('contract/pdf/{contract}', [PdfController::class, 'contractPdf'])->name('contract.pdf');
    });

    Route::middleware('permission:Ver caja chica')->group(function () {
        Route::get('accounts-boxes', AccountBoxComponent::class)->name('accounts.box');
        Route::get('account/box/pdf', [PdfController::class, 'accountBox'])->name('account.box.pdf');
    });

    Route::middleware('permission:Crear caja chica|Editar caja chica')->group(function () {
        Route::get('accounts-boxes/form/{id?}', BoxFormComponent::class)->name('accounts.box.form');
    });

    Route::middleware('permission:PDF caja chica')->group(function () {
        Route::get('movement/box/pdf/{id}', [PdfController::class, 'receiptBox'])->name('receipt.box.pdf');
    });

    Route::middleware('permission:Ver retenciones')->group(function () {
        Route::get('retentions', RetentionComponent::class)->name('retentions');
        Route::get('retention/pdf/form/{id}', [PdfController::class, 'retentionForm'])->name('retention.pdf.form');
    });

    Route::middleware('permission:Crear retenciones')->group(function () {
        Route::get('retentions/form/{id?}', RetentionComponentForm::class)->name('retention.form');
    });

    Route::middleware('permission:Exportar retenciones')->group(function () {
        Route::get('retention-month/excel/{date}/{type}', [ExcelController::class, 'retentionMonth'])->name('retention.month.excel');
    });

    Route::middleware('permission:Ver proveedores')->group(function () {
        Route::get('suppliers', SupplierComponent::class)->name('suppliers');
    });

    Route::middleware('permission:Ver clientes')->group(function () {
        Route::get('customers', CustomerComponent::class)->name('customers');
    });

    Route::middleware('permission:Ver departamentos')->group(function () {
        Route::get('departments', DepartamentComponent::class)->name('departments');
    });

    Route::middleware('permission:Ver cooperativas')->group(function () {
        Route::get('cooperatives', CooperativeComponent::class)->name('cooperatives');
    });

    Route::middleware('permission:Ver liquidaciones')->group(function () {
        Route::get('liquidations', LiquidationComponent::class)->name('liquidations');
        Route::get('liquidations/form/{id?}', LiquidationForm::class)->name('liquidation.form');
        Route::get('liquidation/pdf/{liquidation}', [PdfController::class, 'liquidationPdf'])->name('liquidation.pdf');
        Route::get('liquidation/simple/{liquidation}', [PdfController::class, 'liquidationSimplePdf'])->name('liquidation.simple.pdf');
    });

    Route::middleware('permission:Ver cotizaciones')->group(function () {
        Route::get('cotizaciones', CotizacionComponent::class)->name('cotizaciones');
    });

    Route::middleware('permission:Ver reportes')->group(function () {
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('retentions', RetentionReportsComponent::class)->name('retentions');
            Route::get('box', BoxReportsComponent::class)->name('box');
            Route::get('bank-book', BankBookReportsComponent::class)->name('bank-book');
            Route::get('liquidations', LiquidationReportsComponent::class)->name('liquidations');
        });
    });

    Route::middleware('permission:Ver usuarios')->group(function () {
        Route::get('users', UserComponent::class)->name('users');
    });

    Route::middleware('permission:Ver roles')->group(function () {
        Route::get('roles', RoleComponent::class)->name('roles');
    });

    Route::middleware('permission:Asignación de permisos')->group(function () {
        Route::get('permissions', PermissionComponent::class)->name('permissions');
    });

    Route::middleware('permission:Ver libro de bancos')->group(function () {
        Route::get('transactions', TransactionComponent::class)->name('transactions');
        Route::get('transactions/{id}/{date}', ViewComponent::class)->name('transactions.view');
    });

    Route::middleware('permission:Crear movimientos de libro de bancos')->group(function () {
        Route::get('transactions/form/{date_account}/{account_id}/{id?}', TransactionFormComponent::class)->name('transactions.form');
    });

    Route::middleware('permission:PDF libro de bancos')->group(function () {
        Route::get('transaction/pdf/form/{start}/{end}/{id}', [PdfController::class, 'transactionAccount'])->name('transaction.account.pdf');
        Route::get('transaction/pdf/{id}', [PdfController::class, 'receiptTransaction'])->name('receipt.transaction.pdf');
    });

    Route::middleware('permission:Excel libro de bancos')->group(function () {
        Route::get('transaction/excel/form/{start}/{end}/{id}', [ExcelController::class, 'transactionAccount'])->name('transaction.account.excel');
    });

    /* ── Inventario ── */
    Route::middleware('permission:Ver productos')->group(function () {
        Route::get('inventory/products', ProductComponent::class)->name('inventory.products');
    });

    Route::middleware('permission:Crear productos|Editar productos')->group(function () {
        Route::get('inventory/products/form/{id?}', ProductFormComponent::class)->name('inventory.products.form');
    });

    Route::middleware('permission:Ver kardex')->group(function () {
        Route::get('inventory/kardex/{product}', KardexComponent::class)->name('inventory.kardex');
    });

    Route::middleware('permission:Ver banco inventario')->group(function () {
        Route::get('inventory/bank', BankComponent::class)->name('inventory.bank');
    });

    Route::middleware('permission:Ver entradas inventario')->group(function () {
        Route::get('inventory/purchases', PurchaseComponent::class)->name('inventory.purchases');
    });

    Route::middleware('permission:Crear entradas inventario|Editar entradas inventario')->group(function () {
        Route::get('inventory/purchases/form/{id?}', PurchaseFormComponent::class)->name('inventory.purchases.form');
    });

    /* ── RRHH ── */
    Route::prefix('rrhh')->name('rrhh.')->group(function () {
        Route::middleware('permission:Ver empleados')->group(function () {
            Route::get('empleados', \App\Livewire\Rrhh\EmployeeComponent::class)->name('employees');
        });

        Route::middleware('permission:Ver planillas')->group(function () {
            Route::get('planillas', \App\Livewire\Rrhh\PayrollComponent::class)->name('payrolls');
        });

        Route::middleware('permission:Crear planillas|Editar planillas')->group(function () {
            Route::get('planillas/form/{id}', \App\Livewire\Rrhh\PayrollFormComponent::class)->name('payrolls.form');
        });

        Route::middleware('permission:Ver vacaciones')->group(function () {
            Route::get('vacaciones', \App\Livewire\Rrhh\VacationComponent::class)->name('vacations');
        });
    });
});

require __DIR__.'/settings.php';
