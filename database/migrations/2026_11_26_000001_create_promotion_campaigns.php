<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('promotion_campaigns', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('slug')->unique();
            $table->string('headline'); $table->text('intro')->nullable(); $table->text('terms')->nullable();
            $table->string('banner_image')->nullable(); $table->string('mobile_banner_image')->nullable(); $table->string('product_banner_image')->nullable();
            $table->boolean('is_active')->default(false); $table->boolean('show_product_banner')->default(false); $table->integer('priority')->default(0);
            $table->timestamp('starts_at')->nullable(); $table->timestamp('ends_at')->nullable(); $table->timestamps();
        });
        Schema::create('promotion_campaign_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('promotion_campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true); $table->unsignedInteger('sort_order')->default(0); $table->string('badge',60)->nullable(); $table->timestamps();
            $table->unique(['promotion_campaign_id','product_id'],'campaign_product_unique');
        });
    }
    public function down(): void { Schema::dropIfExists('promotion_campaign_items'); Schema::dropIfExists('promotion_campaigns'); }
};
