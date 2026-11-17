<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->date('purchase_date')->nullable();
            $table->string('order_source', 20)->default('online');
            $table->string('activation_token_hash', 64)->nullable()->unique();
            $table->timestamp('activation_token_expires_at')->nullable();
        });
        Schema::create('warranty_activations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_variant_id')->constrained()->restrictOnDelete();
            $table->string('customer_phone', 20)->index();
            $table->string('customer_name', 120);
            $table->unsignedInteger('quantity');
            $table->string('serial', 255)->nullable();
            $table->date('purchase_date');
            $table->unsignedInteger('warranty_days');
            $table->date('expires_at');
            $table->timestamp('activated_at');
            $table->string('activated_by', 20);
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->unique(['order_id', 'product_variant_id'], 'warranty_order_variant_unique');
        });
        Schema::create('warranty_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->restrictOnDelete();
            $table->string('customer_phone', 20)->index();
            $table->string('customer_name', 120);
            $table->string('serial', 255)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->date('purchase_date')->nullable();
            $table->text('review_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('order_id')->nullable()->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warranty_requests');
        Schema::dropIfExists('warranty_activations');
        Schema::table('orders', fn (Blueprint $table) => $table->dropColumn(['purchase_date', 'order_source', 'activation_token_hash', 'activation_token_expires_at']));
        // Keep user_id nullable: offline orders must not be assigned to an invented account.
    }
};
