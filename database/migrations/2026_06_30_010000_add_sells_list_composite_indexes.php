<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Composite indexes for the dominant transactions query shapes.
 *
 * transactions already has ~26 SINGLE-column indexes. MySQL uses only one per
 * query, so the sells/purchase list (TransactionUtil::getListSells) seeks on
 * business_id alone then scans+filesorts the rest. These composites let the
 * WHERE (business_id, type, status) and the date ordering be satisfied by one
 * index — the canonical UltimatePOS large-dataset fix.
 *
 * Only relevant at scale; on a small table the planner may ignore them. Net
 * cost is 2 extra indexes on a write-hot table — acceptable for the read win.
 */
return new class extends Migration
{
    public function up()
    {
        // ponytail: idempotent — optician perf-index migration may run before/after
        // this, and prod may already be partially migrated. Skip what exists.
        $this->addIndex('transactions', ['business_id', 'type', 'status', 'transaction_date'], 'transactions_biz_type_status_date_idx');
        $this->addIndex('transactions', ['business_id', 'type', 'payment_status'], 'transactions_biz_type_pay_status_idx');
    }

    public function down()
    {
        $this->dropIndex('transactions', 'transactions_biz_type_status_date_idx');
        $this->dropIndex('transactions', 'transactions_biz_type_pay_status_idx');
    }

    private function indexExists(string $table, string $index): bool
    {
        return ! empty(DB::select(
            'SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ? LIMIT 1',
            [$table, $index]
        ));
    }

    private function addIndex(string $table, array $cols, string $name): void
    {
        if (! $this->indexExists($table, $name)) {
            Schema::table($table, fn (Blueprint $t) => $t->index($cols, $name));
        }
    }

    private function dropIndex(string $table, string $name): void
    {
        if ($this->indexExists($table, $name)) {
            Schema::table($table, fn (Blueprint $t) => $t->dropIndex($name));
        }
    }
};
