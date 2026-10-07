<?php

namespace Tests\Unit;

use App\Utils\TransactionUtil;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Facade;
use Mockery;
use PHPUnit\Framework\TestCase;

class SalePurchaseOrderTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function saleInput(): array
    {
        return [
            'invoice_no' => 'SALE-001', 'status' => 'draft', 'location_id' => 1,
            'contact_id' => 1, 'customer_group_id' => null, 'tax_rate_id' => null,
            'discount_type' => 'fixed', 'discount_amount' => 0, 'final_total' => 100,
            'transaction_date' => '2026-10-07 12:00:00', 'commission_agent' => null,
            'pay_term_number' => 1, 'pay_term_type' => 'days',
        ];
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_create_passes_purchase_order_fields_to_persistence(): void
    {
        $input = $this->saleInput() + ['purchase_order_no' => 'PO-001', 'purchase_order_date' => '2026-10-06'];
        $model = Mockery::mock('alias:App\\Transaction');
        $model->shouldReceive('create')->once()->with(Mockery::on(function ($attributes) {
            return $attributes['purchase_order_no'] === 'PO-001'
                && $attributes['purchase_order_date'] === '2026-10-06';
        }))->andReturn((object) ['id' => 1]);
        $sale = (new TransactionUtil)->createSellTransaction(1, $input, ['total_before_tax' => 100, 'tax' => 0], 1, false);
        $this->assertSame(1, $sale->id);
    }

    public function test_updates_can_replace_clear_and_preserve_order_fields(): void
    {
        $sale = new class {
            public $status = 'draft';
            public $invoice_no = 'SALE-001';
            public $document = null;
            public $source = null;
            public $attributes = ['purchase_order_no' => 'PO-OLD', 'purchase_order_date' => '2026-10-01'];
            public function fill($attributes) { $this->attributes = array_merge($this->attributes, $attributes); }
            public function update() {}
        };
        $util = new TransactionUtil;
        $totals = ['total_before_tax' => 100, 'tax' => 0];
        $util->updateSellTransaction($sale, 1, $this->saleInput(), $totals, 1, false);
        $this->assertSame('PO-OLD', $sale->attributes['purchase_order_no']);
        $this->assertSame('2026-10-01', $sale->attributes['purchase_order_date']);
        $input = $this->saleInput() + ['purchase_order_no' => 'PO-NEW', 'purchase_order_date' => '2026-10-07'];
        $util->updateSellTransaction($sale, 1, $input, $totals, 1, false);
        $this->assertSame('PO-NEW', $sale->attributes['purchase_order_no']);
        $this->assertSame('2026-10-07', $sale->attributes['purchase_order_date']);
        $input['purchase_order_no'] = null;
        $input['purchase_order_date'] = null;
        $util->updateSellTransaction($sale, 1, $input, $totals, 1, false);
        $this->assertNull($sale->attributes['purchase_order_no']);
        $this->assertNull($sale->attributes['purchase_order_date']);
    }

    public function test_migration_preserves_existing_sales_and_supports_rollback(): void
    {
        $db = new Capsule;
        $db->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        $db->setAsGlobal();
        $db->bootEloquent();
        $db->getContainer()->instance('db', $db->getDatabaseManager());
        $db->getContainer()->bind('db.schema', function () use ($db) { return $db->schema(); });
        Facade::setFacadeApplication($db->getContainer());
        $db->schema()->create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_no');
        });
        $db->table('transactions')->insert(['invoice_no' => 'EXISTING']);
        $migration = require __DIR__.'/../../database/migrations/2026_10_07_000001_add_purchase_order_fields_to_transactions_table.php';
        $migration->up();
        $existing = $db->table('transactions')->first();
        $this->assertNull($existing->purchase_order_no);
        $this->assertNull($existing->purchase_order_date);
        $db->table('transactions')->where('id', $existing->id)->update(['purchase_order_no' => 'PO-001', 'purchase_order_date' => '2026-10-06']);
        $this->assertSame('2026-10-06', $db->table('transactions')->first()->purchase_order_date);
        $migration->down();
        $this->assertFalse($db->schema()->hasColumn('transactions', 'purchase_order_no'));
        $this->assertFalse($db->schema()->hasColumn('transactions', 'purchase_order_date'));
        $this->assertSame('EXISTING', $db->table('transactions')->first()->invoice_no);
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
    }
}
