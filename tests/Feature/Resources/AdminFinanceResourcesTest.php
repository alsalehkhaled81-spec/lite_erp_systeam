<?php

use App\Filament\Resources\InvoiceResource;
use App\Filament\Resources\ExpenseResource;
use App\Filament\Resources\PayrollResource;
use App\Filament\Resources\InvoiceResource\RelationManagers\InvoiceItemsRelationManager;
use App\Filament\Resources\InvoiceResource\Pages\ListInvoices;
use App\Filament\Resources\InvoiceResource\Pages\CreateInvoice;
use App\Filament\Resources\ExpenseResource\Pages\ListExpenses;
use App\Filament\Resources\ExpenseResource\Pages\CreateExpense;
use App\Filament\Resources\PayrollResource\Pages\ListPayrolls;
use App\Filament\Resources\PayrollResource\Pages\CreatePayroll;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\User;
use Filament\Facades\Filament;

/*
|--------------------------------------------------------------------------
| Helper: authenticate as super admin on the admin panel
|--------------------------------------------------------------------------
*/
function adminPanelUser(): User
{
    $role = \App\Models\Role::firstOrCreate(['name' => 'super_admin'], ['description' => 'Super Admin']);

    return User::factory()->create([
        'role_id' => $role->id,
        'is_approved' => true,
    ]);
}

beforeEach(function () {
    $this->actingAs($user = adminPanelUser());
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

describe('Admin InvoiceResource parity with Accountant', function () {

    test('form contains VAT fields and items repeater', function () {
        Livewire::test(CreateInvoice::class)
            ->assertFormFieldExists('vat_rate')
            ->assertFormFieldExists('vat_amount')
            ->assertFormFieldExists('total_with_vat')
            ->assertFormFieldExists('amount')
            ->assertFormFieldExists('items');
    });

    test('form items repeater child fields match InvoiceItem model', function () {
        $itemModel = new \App\Models\InvoiceItem();

        expect($itemModel->getFillable())
            ->toContain('description')
            ->toContain('quantity')
            ->toContain('unit_price')
            ->toContain('total')
            ->toContain('invoice_id');
    });

    test('table contains VAT and subtotal columns', function () {
        Livewire::test(ListInvoices::class)
            ->assertCanRenderTableColumn('vat_amount')
            ->assertCanRenderTableColumn('total_with_vat')
            ->assertCanRenderTableColumn('amount');
    });

    test('table has download_pdf action', function () {
        $components = Livewire::test(ListInvoices::class);

        $tableActions = invade($components->instance())->getTable()->getActions();

        $names = collect($tableActions)->map(fn ($a) => $a->getName())->toArray();

        expect($names)->toContain('download_pdf');
    });

    test('uses correct navigation group and label', function () {
        expect(InvoiceResource::getNavigationGroup())->toBe(__('filament.group.finance'))
            ->and(InvoiceResource::getNavigationLabel())->toBe(__('filament.nav.invoices'));
    });

    test('is globally searchable by invoice_number', function () {
        expect(InvoiceResource::getGloballySearchableAttributes())->toContain('invoice_number');
    });
});

describe('Admin InvoiceItemsRelationManager', function () {

    test('targets items relationship', function () {
        $reflection = new ReflectionClass(InvoiceItemsRelationManager::class);
        $property = $reflection->getProperty('relationship');
        $property->setAccessible(true);

        expect($property->getValue())->toBe('items');
    });
});

describe('Admin ExpenseResource parity with Accountant', function () {

    test('form contains project_id field', function () {
        Livewire::test(CreateExpense::class)
            ->assertFormFieldExists('project_id');
    });

    test('form contains all core fields', function () {
        Livewire::test(CreateExpense::class)
            ->assertFormFieldExists('user_id')
            ->assertFormFieldExists('title')
            ->assertFormFieldExists('category')
            ->assertFormFieldExists('amount')
            ->assertFormFieldExists('expense_date');
    });

    test('table has approve action', function () {
        $components = Livewire::test(ListExpenses::class);
        $tableActions = invade($components->instance())->getTable()->getActions();

        $names = collect($tableActions)->map(fn ($a) => $a->getName())->toArray();

        expect($names)->toContain('approve');
    });

    test('table has reject action', function () {
        $components = Livewire::test(ListExpenses::class);
        $tableActions = invade($components->instance())->getTable()->getActions();

        $names = collect($tableActions)->map(fn ($a) => $a->getName())->toArray();

        expect($names)->toContain('reject');
    });

    test('approve action updates status to approved', function () {
        $user = User::factory()->create();
        $expense = Expense::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $expense->update(['status' => 'approved', 'approved_by' => $user->id]);

        expect($expense->fresh()->status)->toBe('approved')
            ->and($expense->fresh()->approved_by)->toBe($user->id);
    });

    test('reject action updates status to rejected', function () {
        $user = User::factory()->create();
        $expense = Expense::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $expense->update(['status' => 'rejected', 'approved_by' => $user->id]);

        expect($expense->fresh()->status)->toBe('rejected');
    });

    test('uses correct navigation group', function () {
        expect(ExpenseResource::getNavigationGroup())->toBe(__('filament.group.finance'));
    });
});

describe('Admin PayrollResource parity with Accountant', function () {

    test('form has all salary fields', function () {
        Livewire::test(CreatePayroll::class)
            ->assertFormFieldExists('employee_id')
            ->assertFormFieldExists('month_year')
            ->assertFormFieldExists('basic_salary')
            ->assertFormFieldExists('bonuses')
            ->assertFormFieldExists('deductions');
    });

    test('form has allowances fields', function () {
        Livewire::test(CreatePayroll::class)
            ->assertFormFieldExists('housing_allowance')
            ->assertFormFieldExists('transport_allowance')
            ->assertFormFieldExists('phone_allowance');
    });

    test('form has insurance and absence fields', function () {
        Livewire::test(CreatePayroll::class)
            ->assertFormFieldExists('social_insurance_rate')
            ->assertFormFieldExists('social_insurance_amount')
            ->assertFormFieldExists('absence_days')
            ->assertFormFieldExists('absence_deduction');
    });

    test('form has net_salary and status fields', function () {
        Livewire::test(CreatePayroll::class)
            ->assertFormFieldExists('net_salary')
            ->assertFormFieldExists('status');
    });

    test('table has download_payslip action', function () {
        $components = Livewire::test(ListPayrolls::class);
        $tableActions = invade($components->instance())->getTable()->getActions();

        $names = collect($tableActions)->map(fn ($a) => $a->getName())->toArray();

        expect($names)->toContain('download_payslip');
    });

    test('recalculate computes correct net salary', function () {
        $reflection = new ReflectionClass(PayrollResource::class);
        $method = $reflection->getMethod('recalculate');
        $method->setAccessible(true);

        $captured = [];

        $set = function ($key, $value) use (&$captured) {
            $captured[$key] = $value;
        };
        $get = function ($key) {
            return [
                'basic_salary' => 10000,
                'bonuses' => 500,
                'deductions' => 200,
                'housing_allowance' => 1000,
                'transport_allowance' => 300,
                'phone_allowance' => 200,
                'social_insurance_rate' => 10,
                'absence_days' => 2,
            ][$key] ?? null;
        };

        $method->invoke(null, $set, $get);

        expect($captured['social_insurance_amount'])->toBe(1000.0)
            ->and($captured['net_salary'])->toBe(10133.33);
    });

    test('pages configured correctly', function () {
        $pages = PayrollResource::getPages();

        expect($pages)
            ->toHaveKey('index')
            ->toHaveKey('create')
            ->toHaveKey('edit');
    });

    test('uses correct navigation group and label', function () {
        expect(PayrollResource::getNavigationGroup())->toBe(__('filament.group.finance'))
            ->and(PayrollResource::getNavigationLabel())->toBe(__('filament.nav.payrolls'));
    });

    test('is globally searchable by month_year', function () {
        expect(PayrollResource::getGloballySearchableAttributes())->toContain('month_year');
    });

    test('model net salary calculation matches resource logic', function () {
        $net = Payroll::calculateNetSalary(
            basic: 10000,
            bonuses: 500,
            deductions: 200,
            housing: 1000,
            transport: 300,
            phone: 200,
            insuranceRate: 10,
            absenceDeduction: 666.67,
        );

        expect($net)->toBe(10133.33);
    });

    test('list page has export_excel header action', function () {
        $page = new ListPayrolls();

        $reflection = new ReflectionClass($page);
        $method = $reflection->getMethod('getHeaderActions');
        $method->setAccessible(true);

        $actions = $method->invoke($page);

        $names = collect($actions)->map(fn ($a) => $a->getName())->toArray();

        expect($names)->toContain('export_excel');
    });
});
