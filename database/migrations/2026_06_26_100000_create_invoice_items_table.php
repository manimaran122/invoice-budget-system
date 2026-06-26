<?php

use App\Enums\InvoiceType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_type', 20);
            $table->unsignedBigInteger('invoice_id');
            $table->foreignId('product_service_id')->nullable()->constrained('product_services')->nullOnDelete();
            $table->string('description');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();

            $table->index(['invoice_type', 'invoice_id']);
        });

        $this->backfillInvoiceItems(InvoiceType::Purchase->value, 'purchase_invoices');
        $this->backfillInvoiceItems(InvoiceType::Sales->value, 'sales_invoices');
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }

    private function backfillInvoiceItems(string $invoiceType, string $tableName): void
    {
        DB::table($tableName)
            ->orderBy('id')
            ->get()
            ->each(function ($invoice) use ($invoiceType) {
                DB::table('invoice_items')->insert([
                    'invoice_type' => $invoiceType,
                    'invoice_id' => $invoice->id,
                    'product_service_id' => null,
                    'description' => 'Invoice Amount',
                    'quantity' => 1,
                    'price' => $invoice->subtotal,
                    'tax' => $invoice->tax,
                    'discount' => $invoice->discount,
                    'total' => $invoice->total,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }
};
